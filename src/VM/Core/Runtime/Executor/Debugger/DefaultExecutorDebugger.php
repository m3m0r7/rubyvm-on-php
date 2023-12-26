<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence\Insn as Ruby3_2_Insn;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence\Insn as Ruby3_3_Insn;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Exception\RubyVMException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\StreamOutput;

class DefaultExecutorDebugger implements DebuggerInterface
{
    use DebugFormat;

    /**
     * @var array<array{InsnInterface, ContextInterface, int, null|string}>
     */
    protected array $snapshots = [];

    protected int $currentMemoryUsage;

    protected ContextInterface $context;

    public function __construct()
    {
        $this->currentMemoryUsage = memory_get_usage(false);
    }

    public function __debugInfo(): array
    {
        return [];
    }

    public function append(InsnInterface $insn, ContextInterface $context): void
    {
        $this->snapshots[] = [
            $insn,
            $context,
            memory_get_usage(false) - $this->currentMemoryUsage,
            $this->makeDetails($insn, $context),
        ];

        $this->currentMemoryUsage = memory_get_usage(false);
    }

    public function showExecutedOperations(): void
    {
        $table = new Table(
            new StreamOutput(
                STDOUT,
            ),
        );

        $table->setHeaders(
            [
                'PC',
                'CALLEE',
                'INSN',
                'CURRENT STACKS',
                'LOCAL TABLES',
            ]
        );
        $table->setColumnMaxWidth(0, 3)
            ->setColumnWidth(0, 3);

        $table->setColumnMaxWidth(1, 30)
            ->setColumnWidth(1, 30);

        $table->setColumnMaxWidth(2, 30);
        $table->setColumnMaxWidth(3, 60);
        $table->setColumnMaxWidth(4, 60);

        /**
         * @var Insn             $insn
         * @var ContextInterface $context
         * @var int              $memoryUsage
         */
        foreach ($this->snapshots as $index => [$insn, $context, $memoryUsage, $insnDetails]) {
            if ($index > 0) {
                $table->addRows([
                    new TableSeparator(),
                ]);
            }

            $table->addRow([
                $context->programCounter()->pos(),
                implode(' -> ', $context->traces()),
                sprintf(
                    '[0x%02x] %s %s',
                    $insn->value,
                    strtolower($insn->name),
                    $insnDetails ? "({$insnDetails})" : '',
                ),
                (string) $context->vmStack(),
                (string) $context->environmentTable(),
            ]);
        }

        $table->render();
    }

    private function makeDetails(InsnInterface $insn, ContextInterface $context): ?string
    {
        $context = $context->createSnapshot();

        // TODO: Rewrite here to depending on running kernel, but here is hard coded.
        return match ($insn) {
            Ruby3_2_Insn::SEND,
            Ruby3_2_Insn::OPT_SEND_WITHOUT_BLOCK,
            Ruby3_3_Insn::SEND,
            Ruby3_3_Insn::OPT_SEND_WITHOUT_BLOCK => $this->debugCallMethod($context),
            Ruby3_2_Insn::GETLOCAL,
            Ruby3_2_Insn::GETLOCAL_WC_0,
            Ruby3_2_Insn::GETLOCAL_WC_1,
            Ruby3_2_Insn::SETLOCAL,
            Ruby3_2_Insn::SETLOCAL_WC_0,
            Ruby3_2_Insn::SETLOCAL_WC_1,
            Ruby3_3_Insn::GETLOCAL,
            Ruby3_3_Insn::GETLOCAL_WC_0,
            Ruby3_3_Insn::GETLOCAL_WC_1,
            Ruby3_3_Insn::SETLOCAL,
            Ruby3_3_Insn::SETLOCAL_WC_0,
            Ruby3_3_Insn::SETLOCAL_WC_1 => $this->debugLocalVariable($context),
            default => null,
        };
    }

    private function debugLocalVariable(ContextInterface $context): string
    {
        $currentPos = $context->programCounter()->pos();

        $number = $context
            ->instructionSequence()
            ->body()
            ->info()
            ->operationEntries()
            ->get($currentPos + 1)
            ->operand;

        return sprintf('ref: %d', $number->valueOf());
    }

    private function debugCallMethod(ContextInterface $context): string
    {
        $details = '';
        $currentPos = $context->programCounter()->pos();
        $vmStack = clone $context->vmStack();

        /**
         * @var Operand $callDataOperand
         */
        $callDataOperand = $context
            ->instructionSequence()
            ->body()
            ->info()
            ->operationEntries()
            ->get($currentPos + 1);

        if (!$callDataOperand->operand instanceof CallInfoInterface) {
            throw new RubyVMException('Unexpected to load operand');
        }

        $arguments = [];
        for ($i = 0; $i < $callDataOperand->operand->callData()->argumentsCount(); ++$i) {
            $arguments[] = $vmStack->pop();
        }

        /**
         * @var Operand $class
         */
        $class = $vmStack->pop();

        $context->programCounter()
            ->set($currentPos);

        return sprintf(
            '%s#%s(%s)',
            ClassHelper::nameBy($class->operand),
            (string) $callDataOperand
                ->operand
                ->callData()
                ->mid()
                ->object,
            self::getEntriesAsString($arguments),
        );
    }

    public function enter(ContextInterface $context): void
    {
        $this->context = $context;
    }

    public function leave(ExecutedResult $result): void {}

    public function process(InsnInterface $insn, ContextInterface $context): void {}
}
