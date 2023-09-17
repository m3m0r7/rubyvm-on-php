<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\StreamOutput;

class ExecutorDebugger
{
    protected array $snapshots = [];
    protected int $currentMemoryUsage;
    protected ContextInterface $context;

    public function __construct()
    {
        $this->currentMemoryUsage = memory_get_usage(false);
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
            ->setColumnWidth(0, 3)
        ;

        $table->setColumnMaxWidth(1, 30)
            ->setColumnWidth(1, 30)
        ;

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
             * @var OperandEntry $callDataOperand
             */
            $callDataOperand = $context
                ->instructionSequence()
                ->operations()
                ->get($currentPos + 1)
            ;

            $arguments = [];
            for ($i = 0; $i < $callDataOperand->operand->callData()->argumentsCount(); ++$i) {
                $arguments[] = $vmStack->pop();
            }

            /**
             * @var OperandEntry|RubyClassImplementationInterface $class
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
                    ->object
                    ->symbol,
                implode(
                    ', ',
                    array_map(
                        fn ($argument) => match ($argument::class) {
                            SymbolInterface::class => (string) $argument,
                            OperandEntry::class => (string) $argument->operand->symbol,
                            default => '?',
                        },
                        $arguments,
                    ),
                ),
            );
        }
        if (Insn::GETLOCAL_WC_0 === $insn || Insn::GETLOCAL_WC_1 === $insn || Insn::SETLOCAL_WC_0 === $insn || Insn::SETLOCAL_WC_1 === $insn) {
            $currentPos = $context->programCounter()->pos();

            /**
             * @var NumberSymbol $number
             */
            $number = $context
                ->instructionSequence()
                ->operations()
                ->get($currentPos + 1)
                ->operand
                ->symbol
            ;

            return sprintf('ref: %d', $number->number);
        }

        return null;
    }
}
