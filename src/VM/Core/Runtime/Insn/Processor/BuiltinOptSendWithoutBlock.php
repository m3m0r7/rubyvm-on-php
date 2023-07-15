<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ArgumentTranslatable;
use RubyVM\VM\Core\Runtime\Executor\CallInfoEntryInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

class BuiltinOptSendWithoutBlock implements OperationProcessorInterface
{
    use Validatable;
    use ArgumentTranslatable;

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

        $callDataOperand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(
            OperandEntry::class,
            $callDataOperand,
        );

        $this->validateType(
            CallInfoEntryInterface::class,
            $callDataOperand->operand,
        );

        /**
         * @var CallInfoEntryInterface $callInfo
         */
        $callInfo = $callDataOperand->operand;
        $arguments = [];
        for ($i = 0; $i < $callInfo->callData()->argumentsCount(); $i++) {
            $arguments[] = $operand = $this->context->vmStack()->pop();
        }
        $class = $this->context->vmStack()->pop();

        $this->validateType(
            OperandEntry::class,
            $class,
        );

        $this->validateType(
            OperandEntry::class,
            ...$arguments,
        );

        /**
         * @var StringSymbol $symbol
         */
        $symbol = $callInfo
            ->callData()
            ->mid()
            ->object
            ->symbol;

        $this->validateType(
            StringSymbol::class,
            $symbol,
        );

        /**
         * @var MainInterface $self
         */
        $self = $class->operand;

        $self->{$symbol->string}(...$this->translateForArguments(...$arguments));

        return ProcessedStatus::SUCCESS;
    }
}
