<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RubyVMException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\StreamOutput;

class ExecutorDebugger
{
    /**
     * @var array<array{Insn, ContextInterface, int, null|string}>
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

    public function bindContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    public function append(Insn $insn, ContextInterface $context): void
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
        $handle = fopen('php://stdout', 'rw+');

        if ($handle === false) {
            throw new RubyVMException('Unexpected to read stream');
        }

        if (false === $this->context->shouldProcessedRecords()) {
            fwrite($handle, "No processed records enabled.\n");

            return;
        }

        $table = new Table(
            new StreamOutput($handle),
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

    private function makeDetails(Insn $insn, ContextInterface $context): ?string
    {
        $context = $context->createSnapshot();
        if (Insn::OPT_SEND_WITHOUT_BLOCK === $insn) {
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

            $context->programCounter()->set($currentPos);

            return sprintf(
                '%s#%s(%s)',
                ClassHelper::nameBy($class->operand),
                (string) $callDataOperand
                    ->operand
                    ->callData()
                    ->mid()
                    ->object,
                implode(
                    ', ',
                    array_map(
                        static fn ($argument) => match ($argument::class) {
                            SymbolInterface::class => (string) $argument,
                            Operand::class => match ($argument->operand::class) {
                                RubyClass::class => (string) $argument->operand,
                                default => '?',
                            },
                            default => '?',
                        },
                        $arguments,
                    ),
                ),
            );
        }

        if (Insn::GETLOCAL_WC_0 === $insn || Insn::GETLOCAL_WC_1 === $insn || Insn::SETLOCAL_WC_0 === $insn || Insn::SETLOCAL_WC_1 === $insn) {
            $currentPos = $context->programCounter()->pos();

            $number = $context
                ->instructionSequence()
                ->body()
                ->info()
                ->operationEntries()
                ->get($currentPos + 1)
                ->operand
            ;

            return sprintf('ref: %d', $number->valueOf());
        }

        return null;
    }
}
