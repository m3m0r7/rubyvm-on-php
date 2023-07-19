<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
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

    public function append(Insn $insn, ContextInterface $context, string $insnDetails = null): void
    {
        $this->snapshots[] = [
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

        if ($this->context->shouldProcessedRecords() === false) {
            fwrite($handle, "No processed records enabled.\n");
            return;
        }

        $table = new Table(
            new StreamOutput($handle),
        );

        $table->setHeaders(
            [
            'PROGRAM COUNTER',
            'INSN',
            'OPCODE',
            'PREVIOUS STACKS',
            'REGISTERED LOCAL TABLES',
            'MEMORY',
            ]
        );

        /**
         * @var Insn $insn
         * @var ContextInterface $context
         * @var int $memoryUsage
         */
        foreach ($this->snapshots as [$insn, $context, $memoryUsage, $insnDetails]) {
            $table->addRow([
                $context->programCounter()->pos(),
                strtolower($insn->name) . ($insnDetails ? "({$insnDetails})" : ''),
                sprintf('0x%02x', $insn->value),
                (string) $context->vmStack(),
                (string) $context->environmentTableEntries(),
                sprintf('%.2f KB', ($memoryUsage / 1000)),
            ]);
        }

        $table->render();
    }
}
