<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\Entry\Variable;

trait LocalTable
{
    use Validatable;
    use OperandHelper;

    public function hasLocalTable(int $slotIndex, int $level = 0): bool
    {
        return $this
            ->targetContextByLevel($level)
            ->environmentTable()
            ->has(
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

    public function localTable(int $slotIndex, int $level = 0): ContextInterface|RubyClassInterface
    {
        return $this
            ->targetContextByLevel($level)
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

    public function setLocalTableFromStack(int $slotIndex, int $level = 0, bool $forcibly = false): void
    {
        $operand = $this->stackAsObject();

        $index = LocalTableHelper::computeLocalTableIndex(
            $this->context
                ->instructionSequence()
                ->body()
                ->info()
                ->localTableSize(),
            $slotIndex,
            $level,
        );

        if ($forcibly) {
            $this->targetContextByLevel($level)
                ->environmentTable()
                ->setLead($index, false);
        }

        $localTableSize = $this->context
            ->instructionSequence()
            ->body()
            ->info()
            ->localTableSize();

        $variables = $this->context
            ->instructionSequence()
            ->body()
            ->info()
            ->variables();

        $zeroStartedSlotIndex = $localTableSize - ($slotIndex - Option::VM_ENV_DATA_SIZE) - 1;
        $targetVariable = $variables[$zeroStartedSlotIndex];

        assert($targetVariable instanceof Variable);

        $this->targetContextByLevel($level)
            ->environmentTable()
            ->set(
                $index,
                $operand,
            )->bindName($slotIndex, (string) $targetVariable->id->object);
    }

    private function targetContextByLevel(int $level = 0): ContextInterface
    {
        $targetEnvironmentTableOnContext = $this->context;

        for ($i = 0; $i < $level; ++$i) {
            $targetEnvironmentTableOnContext = $targetEnvironmentTableOnContext
                ->parentContext();

            assert($targetEnvironmentTableOnContext instanceof ContextInterface);
        }

        return $targetEnvironmentTableOnContext;
    }
}
