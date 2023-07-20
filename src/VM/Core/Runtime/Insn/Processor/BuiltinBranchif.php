<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\SymbolTestable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\OffsetSymbol;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinBranchif implements OperationProcessorInterface
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

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        $offsetSymbol = $this->getOperandAndValidateOffsetSymbol();

        $symbol = $this->getStackAndValidateSymbol();

        if ($this->test($symbol)) {
            $this->context
                ->programCounter()
                ->increase($offsetSymbol->offset);
            return ProcessedStatus::JUMPED;
        }
        return ProcessedStatus::SUCCESS;
    }
}
