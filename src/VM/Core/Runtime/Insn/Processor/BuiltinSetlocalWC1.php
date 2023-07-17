<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class BuiltinSetlocalWC1 implements OperationProcessorInterface
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

        $this->validateType(
            OperandEntry::class,
            $operand,
        );

        $this->validateType(
            Object_::class,
            $operand->operand,
        );

        /**
         * @var SymbolInterface $value
         */
        $operandValue = $this->context->vmStack()->pop();

        $this->validateType(
            OperandEntry::class,
            $operandValue,
        );

        $this->validateType(
            Object_::class,
            $operandValue->operand,
        );

        /**
         * @var NumberSymbol $index
         */
        $index = $operand->operand->symbol;

        $this->context->environmentTableEntries()
            ->get(Option::RSV_TABLE_INDEX_1)
            ->set(
                $index->number,
                $operandValue->operand,
            );

        return ProcessedStatus::SUCCESS;
    }
}
