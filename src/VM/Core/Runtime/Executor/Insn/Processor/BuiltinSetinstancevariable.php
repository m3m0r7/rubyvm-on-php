<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinSetinstancevariable implements OperationProcessorInterface
{
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
        $instanceVar = $this->operandAsID();

        // this is instance variable index
        $ivIndex = $this->operandAsNumber()->valueOf();

        $targetObject = $this->stackAsObject();

        // TODO: is correctly here? we will implement iv or ivc pattern.
        $this->context
            ->self()
            ->userlandHeapSpace()
            ->userlandInstanceVariables()
            ->set($instanceVar->id(), $targetObject);

        return ProcessedStatus::SUCCESS;
    }
}
