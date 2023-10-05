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
use RubyVM\VM\Core\Runtime\Option;

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

        $context = $this->getLocalTableToStack(
            Option::VM_ENV_DATA_SIZE - 1,
            $level,
        );

        foreach ($this->targetContextByLevel($level + 1)->environmentTable() as $index => $value) {
            $this
                ->context
                ->environmentTable()
                ->set($index, $value);
        }

        assert($context instanceof ContextInterface);

        $this->context->vmStack()->push(new Operand(
            new Lambda($context->instructionSequence()),
        ));

        return ProcessedStatus::SUCCESS;
    }
}
