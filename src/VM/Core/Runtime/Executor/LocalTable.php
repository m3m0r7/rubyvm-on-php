<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\InstructionSequence\IDTable;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal\Arithmetic;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function getLocalTableToStack(int $localTableIndex): void
    {
        $index = $this->getOperandAndValidateNumberSymbol()->number;
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
        $index = $this->getOperandAndValidateNumberSymbol()->number;
        $operand = $this->getStackAndValidateObject();

        $this->context->environmentTableEntries()
            ->get($localTableIndex)
            ->set(
                $index,
                clone $operand,
            );
    }
}
