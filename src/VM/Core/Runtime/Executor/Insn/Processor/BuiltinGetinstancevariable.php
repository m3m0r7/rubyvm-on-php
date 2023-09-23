<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinGetinstancevariable implements OperationProcessorInterface
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
        $ivIndex = $this->getOperandAsNumber()->valueOf();

        /**
         * @var RubyClassImplementationInterface $targetObject
         */
        $targetObject = $this->getStackAsClass();

        $this->context->vmStack()->push(
            new Operand(
                $this->context
                    ->self()
                    ->userlandHeapSpace()
                    ->userlandInstanceVariables()
                    ->get($instanceVar->id()),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
