<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\CallInfoEntryInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\ReturnType;
use RubyVM\VM\Core\Runtime\Executor\Translatable;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptSendWithoutBlock implements OperationProcessorInterface
{
    use Validatable;
    use Translatable;

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
         * @var MainInterface|Object_ $targetSymbol
         */
        $targetSymbol = $class->operand;

        /**
         * @var SymbolInterface|ExecutedResult|null $result
         */
        $result = $targetSymbol->{$symbol->string}(...$this->translateForArguments(...$arguments));
        if ($result instanceof Object_) {
            $this->context->vmStack()
                ->push(new OperandEntry($result));
            return ProcessedStatus::SUCCESS;
        }

        if ($result === null) {
            // This is same at UNDEFINED on originally RubyVM
            return ProcessedStatus::SUCCESS;
        }

        if ($result instanceof ExecutedResult) {
            if ($result->throwed) {
                throw $result->throwed;
            }
            if ($result->returnValue !== null) {
                $this->context->vmStack()
                    ->push(new OperandEntry($result->returnValue->toObject()));
            }
            return ProcessedStatus::SUCCESS;
        }
        if ($result instanceof SymbolInterface) {
            $this->context->vmStack()
                ->push(new OperandEntry($result->toObject()));
            return ProcessedStatus::SUCCESS;
        }

        throw new OperationProcessorException(
            'Unreachable here because the opt_send_without_block is not properly implementation'
        );
    }
}
