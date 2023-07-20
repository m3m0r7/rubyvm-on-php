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

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        $newPos = $this->context->programCounter()->increase();

        /**
         * @var OperandEntry $operand
         */
        $operand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(OperandEntry::class, $operand);
        $this->validateType(OffsetSymbol::class, $operand->operand->symbol);

        /**
         * @var OffsetSymbol $offsetSymbol
         */
        $offsetSymbol = $operand->operand->symbol;

        $value = $this->context->vmStack()->pop();

        $this->validateType(OperandEntry::class, $value);
        $this->validateType(Object_::class, $value->operand);
        $symbol = $value->operand->symbol;

        if ($this->unless($symbol)) {
            $this->context
                ->programCounter()
                ->increase($offsetSymbol->offset);
            return ProcessedStatus::JUMPED;
        }

        return ProcessedStatus::SUCCESS;
    }
}
