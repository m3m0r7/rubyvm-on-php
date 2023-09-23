<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
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

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $instanceVar = $this->getOperandAsID();

        // this is instance variable index
        $ivIndex = $this->getOperandAsNumberSymbol()->valueOf();

        $targetObject = $this->getStackAsObject();

        // TODO: is correctly here? we will implement iv or ivc pattern.
        $this->context
            ->self()
            ->userlandHeapSpace()
            ->userlandInstanceVariables()
            ->set($instanceVar->id(), $targetObject);

        return ProcessedStatus::SUCCESS;
    }
}
