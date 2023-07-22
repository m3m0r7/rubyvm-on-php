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
                    ->get(0)
                    ->get($this->calculateLocalTableLevel($index, $localTableIndex)),
            ),
        );
    }

    public function setLocalTableFromStack(int $localTableIndex): void
    {
        $index = $this->getOperandAsNumberSymbol()->number;
        $operand = $this->getStackAsObject();

        $this->context->environmentTableEntries()
            ->get(0)
            ->set(
                $this->calculateLocalTableLevel($index, $localTableIndex),
                clone $operand,
            )
        ;
    }

    private function calculateLocalTableLevel(int $index, int $level): int
    {
        //        if ($level === 0) {
        //            return $index;
        //        }
        //        $newIndex = $index;
        //        for ($i = 0; $i < $level; $i++) {
        //            // cf. vm_get_ep/vm_env_write
        //            $newIndex = $newIndex & ~0x03;
        //        }
        //        return $index - $newIndex;
        return $index;
    }
}
