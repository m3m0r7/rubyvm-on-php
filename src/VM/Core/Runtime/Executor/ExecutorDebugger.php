<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;
use Symfony\Component\Console\Helper\Table;
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

    public function append(string $definitionName, Insn $insn, ContextInterface $context, string $insnDetails = null): void
    {
        $this->snapshots[] = [
            $definitionName,
            $insn,
            $context,
            memory_get_usage(false) - $this->currentMemoryUsage,
            $insnDetails,
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
                'PROGRAM COUNTER',
                'CALLEE',
                'INSN (OPCODE)',
                'PREVIOUS STACKS',
                'REGISTERED LOCAL TABLES',
            ]
        );

        /**
         * @var string           $definitionName,
         * @var Insn             $insn
         * @var ContextInterface $context
         * @var int              $memoryUsage
         */
        foreach ($this->snapshots as [$definitionName, $insn, $context, $memoryUsage, $insnDetails]) {
            $table->addRow([
                $context->programCounter()->pos(),
                $definitionName,
                strtolower($insn->name) . '(' . sprintf('0x%02x', $insn->value) . ') ' . ($insnDetails ? "({$insnDetails})" : ''),
                (string) $context->vmStack(),
                (string) $context->environmentTableEntries(),
            ]);
        }

        $table->render();
    }
}
