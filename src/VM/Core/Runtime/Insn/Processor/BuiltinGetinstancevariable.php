<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\ExtendedClassEntry;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\RubyClassExtendableInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

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
         * @var RubyClassExtendableInterface $targetObject
         */
        $targetObject = $this->getStackAsAny(ExtendedClassEntry::class);

        $this->context->vmStack()->push(
            new OperandEntry(
                $targetObject->getInstanceVariable($instanceVar),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
