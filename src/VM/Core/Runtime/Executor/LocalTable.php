<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\LocalTableHelper;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function getLocalTableToStack(int $slotIndex, int $level): void
    {
        $this->context->vmStack()->push(
            new OperandEntry(
                $this->context
                    ->environmentTableEntries()
                    ->get(0)
                    ->get(
                        LocalTableHelper::computeLocalTableIndex(
                            $this->context
                                ->instructionSequence()
                                ->body()
                                ->data
                                ->localTableSize(),
                            $slotIndex,
                            $level,
                        ),
                    ),
            ),
        );
    }

    public function setLocalTableFromStack(int $slotIndex, int $level): void
    {
        $operand = $this->getStackAsObject();

        $this->context->environmentTableEntries()
            ->get(0)
            ->set(
                LocalTableHelper::computeLocalTableIndex(
                    $this->context
                        ->instructionSequence()
                        ->body()
                        ->data
                        ->localTableSize(),
                    $slotIndex,
                    $level,
                ),
                clone $operand,
            )
        ;
    }
}
