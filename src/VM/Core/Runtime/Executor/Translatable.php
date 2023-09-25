<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\ID;

/**
 * In the C lang case, the language is very flexible for example to int, to unsigned int..., to defined struct and so on.
 * But in other language, especially it based on an object oriented programming languages cannot be flexible.
 * for that reason, this trait class is helping they are problems.
 */
trait Translatable
{
    use Validatable;

    /**
     * @return (CallInfoInterface|ExecutedResult|ID|RubyClassInterface)[]
     */
    public function translateForArguments(Operand ...$operands): array
    {
        $arguments = [];
        foreach ($operands as $operand) {
            $arguments[] = $operand->operand;
        }

        return $arguments;
    }
}
