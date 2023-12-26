<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinPutstring implements OperationProcessorInterface
{
    use OperandHelper;
    private InsnInterface $insn;

    private ContextInterface $context;

    public function prepare(InsnInterface $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(): ProcessedStatus
    {
        $newPos = $this->context->programCounter()->increase();
        $this->context
            ->vmStack()
            ->push(
                $this->context
                    ->instructionSequence()
                    ->body()
                    ->info()
                    ->operationEntries()
                    ->get($newPos),
            );

        return ProcessedStatus::SUCCESS;
    }
}
