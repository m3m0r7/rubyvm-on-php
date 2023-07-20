<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Executor\VMSpecialObjectType;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinPutspecialobject implements OperationProcessorInterface
{
    use OperandHelper;
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

        /**
         * @var OperandEntry $operand
         */
        $operand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(
            OperandEntry::class,
            $operand,
        );

        /**
         * @var NumberSymbol $symbol
         */
        $symbol = $operand->operand->symbol;

        $this->validateType(
            NumberSymbol::class,
            $symbol,
        );

        $type =  VMSpecialObjectType::of($symbol->number);

        $this->context->vmStack()->push(new OperandEntry($this->context->self()));

        return ProcessedStatus::SUCCESS;
    }
}
