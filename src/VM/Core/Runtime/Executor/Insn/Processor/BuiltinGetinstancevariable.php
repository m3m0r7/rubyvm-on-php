<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\YARV\Essential\RubyClassImplementationInterface;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;

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
        $ivIndex = $this->getOperandAsNumberSymbol()->valueOf();

        /**
         * @var RubyClassImplementationInterface $targetObject
         */
        $targetObject = $this->getStackAsClass();

        $this->context->vmStack()->push(
            new OperandEntry(
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
