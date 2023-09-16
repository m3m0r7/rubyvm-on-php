<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Option;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function getLocalTableToStack(int $level): void
    {
        $slotIndex = $this->getOperandAsNumberSymbol()->number;
        $this->context->vmStack()->push(
            new OperandEntry(
                $this->context
                    ->environmentTableEntries()
                    ->get($level)
                    ->get($this->computeLocalTableIndex($slotIndex, $level)),
            ),
        );
    }

    public function setLocalTableFromStack(int $level): void
    {
        $slotIndex = $this->getOperandAsNumberSymbol()->number;
        $operand = $this->getStackAsObject();

        $this->context->environmentTableEntries()
            ->get($level)
            ->set(
                $this->computeLocalTableIndex($slotIndex, $level),
                clone $operand,
            )
        ;
    }

    /**
     * @see https://github.com/ruby/ruby/blob/ruby_3_2/yjit/src/codegen.rs#L1482
     */
    private function computeLocalTableIndex(int $slotIndex, int $level = 0): int
    {
        $localTableSize = $this->context
            ->instructionSequence()
            ->body()
            ->data
            ->localTableSize();

        $op = $slotIndex - Option::VM_ENV_DATA_SIZE;
        $localTableIndex = $localTableSize - $op - 1;
        if ($level > 0) {
            var_dump($slotIndex, $localTableIndex, $localTableIndex + ($level * 4));
        }
        return $localTableIndex + ($level * 4);
    }
}
