<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Lambda;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\LocalTable;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Exception\LocalTableException;

class BuiltinGetblockparamproxy implements OperationProcessorInterface
{
    use LocalTable;
    use OperandHelper;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(): ProcessedStatus
    {
        $slotIndex = $this->operandAsNumber()->valueOf();
        $level = $this->operandAsNumber()->valueOf();

        $context = $this->getLocalTableToStack($slotIndex, $level);
        assert($context instanceof ContextInterface);

        if ($this->hasLocalTable($slotIndex, $level + 1)) {
            $context->vmStack()->push(
                new Operand($this->getLocalTableToStack($slotIndex, $level + 1))
            );

            $this->setLocalTableFromStack($slotIndex, $level, true);
        }

        $this->context->vmStack()->push(new Operand(
            new Lambda($context->instructionSequence()),
        ));

        return ProcessedStatus::SUCCESS;
    }
}
