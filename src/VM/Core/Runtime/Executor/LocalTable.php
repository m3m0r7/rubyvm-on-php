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
                    ->environmentTable()
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

        $this->context->environmentTable()
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
