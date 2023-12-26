<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinGetinstancevariable implements OperationProcessorInterface
{
    use OperandHelper;
    private InsnInterface $insn;

    private ContextInterface $context;

    public function prepare(InsnInterface $insn, ContextInterface $context): void
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

        /**
         * @var RubyClassImplementationInterface $targetObject
         */
        $targetObject = $this->stackAsRubyClass();

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
