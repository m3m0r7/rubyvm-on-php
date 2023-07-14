<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

trait ArgumentTranslatable
{
    use Validatable;

    public function translateForArguments(OperandEntry $operand, OperandEntry ...$operands): array
    {
        if (empty($operands)) {
            /**
             * @var Object_ $object
             */
            $object = $operand->operand;
            $this->validateType(
                Object_::class,
                $object,
            );
            return [$object->symbol];
        }
        $newSymbols = [];
        foreach ([$operand, ...$operands] as $operand) {
            /**
             * @var Object_ $object
             */
            $object = $operand->operand;
            $this->validateType(
                Object_::class,
                $operands,
            );
            $newSymbols[] = $object->symbol;
        }
        return $newSymbols;
    }
}
