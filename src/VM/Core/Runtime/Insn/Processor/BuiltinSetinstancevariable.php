<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

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

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        $instanceVar = $this->getOperandAsID();

        /**
         * @var RubyClassInterface $targetObject
         */
        $targetObject = $this->getStackAsObject()->symbol;
        $targetObject->setInstanceVariable($instanceVar, $this->getStackAsObject());

        // Dummy pop
        $this->getStack();

        return ProcessedStatus::SUCCESS;
    }
}
