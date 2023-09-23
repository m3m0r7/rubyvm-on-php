<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Exception\TranslationException;

/**
 * In the C lang case, the language is very flexible for example to int, to unsigned int..., to defined struct and so on.
 * But in other language, especially it based on an object oriented programming languages cannot be flexible.
 * for that reason, this trait class is helping they are problems.
 */
trait Translatable
{
    use Validatable;

    public function translateForArguments(OperandEntry ...$operands): array
    {
        $arguments = [];
        foreach ($operands as $operand) {
            // @var RubyClass $object
            $arguments[] = $object = $operand->operand;
            $this->validateType(
                RubyClass::class,
                $object,
            );
        }

        return $arguments;
    }
}
