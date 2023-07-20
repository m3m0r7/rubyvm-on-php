<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait Validatable
{
    public function validateType(string $expectClassName, mixed ...$data): void
    {
        foreach ($data as $operandData) {
            if ($operandData instanceof $expectClassName) {
                assert($operandData instanceof $expectClassName);
                continue;
            }

            throw new OperationProcessorException(
                sprintf(
                    'The %s caught unknown operand %s but this validator expects %s',
                    $this->insn->name,
                    get_class($operandData),
                    $expectClassName,
                ),
            );
        }
    }
}
