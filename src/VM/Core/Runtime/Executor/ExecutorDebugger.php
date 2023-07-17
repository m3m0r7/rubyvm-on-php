<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\StreamOutput;

class ExecutorDebugger
{
    protected array $snapshots = [];
    protected int $currentMemoryUsage;

    public function __construct()
    {
        $this->currentMemoryUsage = memory_get_usage(false);
    }

    public function append(Insn $insn, int $time, ContextInterface $context): void
    {
        $this->snapshots[] = [
            $insn,
            clone $context,
            $time,
            memory_get_usage(false) - $this->currentMemoryUsage,
        ];

        $this->currentMemoryUsage = memory_get_usage(false);
    }

    public function showExecutedOperations(): void
    {
        $table = new Table(
            new StreamOutput(
                fopen('php://stdout', 'rw+'),
            )
        );

        $table->setHeaders(
            [
            'PROGRAM COUNTER',
            'INSN',
            'OPCODE',
            'TIME',
            'STACKS',
            'REGISTERED LOCAL TABLES',
            'MEMORY',
            ]
        );

        /**
         * @var Insn $insn
         * @var ContextInterface $context
         * @var int $time
         * @var int $memoryUsage
         */
        foreach ($this->snapshots as [$insn, $context, $time, $memoryUsage]) {
            $table->addRow([
                $context->programCounter()->pos(),
                strtolower($insn->name),
                sprintf('0x%02x', $insn->value),
                "{$time}s",
                count($context->vmStack()),
                count($context->environmentTableEntries()->get(Option::RSV_TABLE_INDEX_0)),
                sprintf("%.2f KB", ($memoryUsage / 1000)),
            ]);
        }

        $table->render();
    }
}
