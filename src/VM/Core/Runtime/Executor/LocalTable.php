<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function getLocalTableToStack(int $localTableIndex): void
    {
        $index = $this->getOperandAsNumberSymbol()->number;
        $this->context->vmStack()->push(
            new OperandEntry(
                $this->context
                    ->environmentTableEntries()
                    ->get($localTableIndex)
                    ->get($index),
            ),
        );
    }

    public function setLocalTableFromStack(int $localTableIndex): void
    {
        $index = $this->getOperandAsNumberSymbol()->number;
        $operand = $this->getStackAsObject();

        $this->context->environmentTableEntries()
            ->get($localTableIndex)
            ->set(
                $index,
                clone $operand,
            )
        ;
    }
}
