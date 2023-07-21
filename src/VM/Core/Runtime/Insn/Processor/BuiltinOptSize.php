<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinOptSize implements OperationProcessorInterface
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
        // No used
        $this->getOperand();

        $recv = $this->getStackAndValidateSymbol();

        if ($recv instanceof ArraySymbol) {
            $this->context->vmStack()->push(
                new OperandEntry(
                    (new NumberSymbol(
                        count($recv),
                    ))->toObject()
                ),
            );
            return ProcessedStatus::SUCCESS;
        }

        throw new OperationProcessorException(
            sprintf(
                'The %s is not compatible type %s',
                strtolower($this->insn->name),
                ClassHelper::nameBy($recv),
            ),
        );
    }
}
