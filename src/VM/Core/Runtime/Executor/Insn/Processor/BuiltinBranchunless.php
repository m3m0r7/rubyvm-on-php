<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\SymbolTestable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;

class BuiltinBranchunless implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;
    use SymbolTestable;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $offsetSymbol = $this->getOperandAsOffsetSymbol();

        $symbol = $this->getStackAsSymbol();

        if ($this->unless($symbol)) {
            $this->context
                ->programCounter()
                ->increase($offsetSymbol->offset);

            return ProcessedStatus::JUMPED;
        }

        return ProcessedStatus::SUCCESS;
    }
}
