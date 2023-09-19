<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;

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

    public function process(SymbolInterface|ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
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
                $targetObject->getInstanceVariable($instanceVar),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
