<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinNewrange implements OperationProcessorInterface
{
    use OperandHelper;
    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(SymbolInterface|ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $flags = $this->getOperandAsNumberSymbol();
        $high = $this->getStackAsNumberSymbol();
        $low = $this->getStackAsNumberSymbol();

        $this->context->vmStack()
            ->push(
                new OperandEntry(
                    (new RangeSymbol(
                        begin: $low,
                        end: $high,
                        excludeEnd: (bool) $flags->valueOf(),
                    ))->toObject(),
                ),
            );

        return ProcessedStatus::SUCCESS;
    }
}
