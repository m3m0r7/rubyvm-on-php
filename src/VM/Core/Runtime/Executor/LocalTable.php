<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function getLocalTableToStack(int $slotIndex, int $level): ContextInterface|RubyClassInterface
    {
        return $this->context
            ->environmentTable()
            ->get(
                LocalTableHelper::computeLocalTableIndex(
                    $this->context
                        ->instructionSequence()
                        ->body()
                        ->info()
                        ->localTableSize(),
                    $slotIndex,
                    $level,
                ),
            );
    }

    public function setLocalTableFromStack(int $slotIndex, int $level): void
    {
        $operand = $this->stackAsObject();

        $this->context->environmentTable()
            ->set(
                LocalTableHelper::computeLocalTableIndex(
                    $this->context
                        ->instructionSequence()
                        ->body()
                        ->info()
                        ->localTableSize(),
                    $slotIndex,
                    $level,
                ),
                $operand,
            );
    }
}
