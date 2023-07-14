<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Exception\OperationProcessorException;

trait Validatable
{
    public function validateType(string $expectClassName, mixed $data, mixed ...$otherData): void
    {
        foreach ([$data, ...$otherData] as $operandData) {
            if ($data instanceof $expectClassName) {
                continue;
            }

            throw new OperationProcessorException(
                sprintf(
                    'The %s caught unknown operand %s but this validator expects %s',
                    $this->insn->name,
                    get_class($operandData),
                    CallDataInterface::class,
                ),
            );
        }
    }
}
