<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\LocalTable;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinSetlocalWC1 implements OperationProcessorInterface
{
    use OperandHelper;
    use LocalTable;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $slotIndex = $this->getOperandAsNumberSymbol()->valueOf();
        $this->setLocalTableFromStack($slotIndex, Option::RSV_TABLE_INDEX_1);

        return ProcessedStatus::SUCCESS;
    }
}
