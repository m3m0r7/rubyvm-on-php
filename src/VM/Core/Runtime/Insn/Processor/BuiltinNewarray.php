<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinNewarray implements OperationProcessorInterface
{
    use OperandHelper;
    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        $entries = [];
        $num = $this->getOperandAsNumberSymbol();

        for ($i = $num->number - 1; $i >= 0; --$i) {
            $entries[$i] = $this->getStackAsNumberSymbol();
        }

        $this->context->vmStack()->push(
            new OperandEntry(
                (new ArraySymbol(array_values($entries)))
                    ->injectVMContext($this->context->kernel())
                    ->toObject(),
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
