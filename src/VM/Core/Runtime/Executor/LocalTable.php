<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

trait LocalTable
{
    use Validatable;

    public function getLocalTableToStack(int $localTableIndex): void
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
         * @var NumberSymbol $index
         */
        $index = $operand->operand->symbol;

        $this->context->vmStack()->push(
            new OperandEntry(
                $this->context
                    ->environmentTableEntries()
                    ->get($localTableIndex)
                    ->get($index->number),
            ),
        );
    }

    public function setLocalTableFromStack(int $localTableIndex): void
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
            ->get($localTableIndex)
            ->set(
                $index->number,
                clone $operandValue->operand,
            );
    }
}
