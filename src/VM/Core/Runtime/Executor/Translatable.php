<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Exception\TranslationException;

/**
 * In the C lang case, the language is very flexible for example to int, to unsigned int..., to defined struct and so on.
 * But in other language, especially it based on an object oriented programming languages cannot be flexible.
 * for that reason, this trait class is helping they are problems.
 */
trait Translatable
{
    use Validatable;

    protected function translateAnEntryToInstructionSequenceNumber(OperationEntry|OperandEntry $entry): int
    {
        if ($entry instanceof OperationEntry) {
            return $entry->insn->value;
        }

        $symbol = $entry->operand->symbol;
        if ($symbol instanceof NumberSymbol) {
            return $symbol->number;
        }

        throw new TranslationException(sprintf('The symbol type cannot translate to number (symbol: %s)', get_class($symbol)));
    }

    public function translateForArguments(OperandEntry ...$operands): array
    {
        $newSymbols = [];
        foreach ($operands as $operand) {
            /**
             * @var Object_ $object
             */
            $object = $operand->operand;
            $this->validateType(
                Object_::class,
                $object,
            );
            $newSymbols[] = $object->symbol;
        }

        return $newSymbols;
    }
}
