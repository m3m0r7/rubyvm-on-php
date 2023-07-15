<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinPutobject implements OperationProcessorInterface
{
    use Validatable;

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
        $newPos = $this->context->programCounter()->increase();

        $operand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(OperandEntry::class, $operand);

        $this->context->vmStack()->push($operand);

        return ProcessedStatus::SUCCESS;
    }
}
