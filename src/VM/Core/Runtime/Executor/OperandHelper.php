<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

/**
 * This trait helps an IDE.
 */
trait OperandHelper
{
    use Validatable;

    private function getOperandAsSymbol(): SymbolInterface
    {
        $operand = $this->getOperandAsAny(
            RubyClass::class,
        );

        return $operand->symbol;
    }

    private function getOperandAsNumberSymbol(): NumberSymbol
    {
        /**
         * @var NumberSymbol $symbol
         */
        $symbol = $this->getOperandAsSymbol();

        $this->validateType(
            NumberSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAsStringSymbol(): StringSymbol
    {
        /**
         * @var StringSymbol $symbol
         */
        $symbol = $this->getOperandAsSymbol();

        $this->validateType(
            StringSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAsFloatSymbol(): FloatSymbol
    {
        /**
         * @var FloatSymbol $symbol
         */
        $symbol = $this->getOperandAsSymbol();

        $this->validateType(
            FloatSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAsOffsetSymbol(): OffsetSymbol
    {
        /**
         * @var OffsetSymbol $symbol
         */
        $symbol = $this->getOperandAsSymbol();

        $this->validateType(
            OffsetSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAsArraySymbol(): ArraySymbol
    {
        /**
         * @var ArraySymbol $symbol
         */
        $symbol = $this->getOperandAsSymbol();

        $this->validateType(
            ArraySymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAsID(): ID
    {
        return $this->getOperandAsAny(ID::class);
    }

    private function getOperandAsMain(): RubyClassInterface
    {
        return $this->getOperandAsAny(RubyClassInterface::class);
    }

    private function getOperandAsCallInfo(): CallInfoEntryInterface
    {
        return $this->getOperandAsAny(CallInfoEntryInterface::class);
    }

    private function getOperandAsExecutedResult(): ExecutedResult
    {
        return $this->getOperandAsAny(ExecutedResult::class);
    }

    private function getOperandAsObject(): RubyClass
    {
        return $this->getOperandAsAny(RubyClass::class)
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace());
    }

    private function getOperand(): OperandEntry
    {
        /**
         * @var OperandEntry $operand
         */
        $operand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($this->context->programCounter()->increase());

        $this->validateType(
            OperandEntry::class,
            $operand,
        );

        return $operand;
    }

    private function getOperandAsAny(string $className): RubyClass|CallInfoEntryInterface|RubyClassInterface|ID|ExecutedResult
    {
        $operand = $this->getOperand();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }

    private function getStackAsSymbol(): SymbolInterface
    {
        $operand = $this->getStackAsAny(
            RubyClass::class
        );

        return $operand->symbol;
    }

    private function getStackAsNumberSymbol(): NumberSymbol
    {
        /**
         * @var NumberSymbol $symbol
         */
        $symbol = $this->getStackAsSymbol();

        $this->validateType(
            NumberSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getStackAsArraySymbol(): ArraySymbol
    {
        /**
         * @var ArraySymbol $symbol
         */
        $symbol = $this->getStackAsSymbol();

        $this->validateType(
            ArraySymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getStackAsStringSymbol(): StringSymbol
    {
        /**
         * @var StringSymbol $symbol
         */
        $symbol = $this->getStackAsSymbol();

        $this->validateType(
            StringSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getStackAsFloatSymbol(): FloatSymbol
    {
        /**
         * @var FloatSymbol $symbol
         */
        $symbol = $this->getStackAsSymbol();

        $this->validateType(
            FloatSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getStackAsOffsetSymbol(): OffsetSymbol
    {
        /**
         * @var OffsetSymbol $symbol
         */
        $symbol = $this->getStackAsSymbol();

        $this->validateType(
            OffsetSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getStackAsID(): ID
    {
        return $this->getStackAsAny(ID::class);
    }

    private function getStackAsClass(): RubyClassInterface
    {
        return $this->getStackAsAny(RubyClassInterface::class);
    }

    private function getStackAsCallInfo(): CallInfoEntryInterface
    {
        return $this->getStackAsAny(CallInfoEntryInterface::class);
    }

    private function getStackAsExecutedResult(): ExecutedResult
    {
        return $this->getStackAsAny(ExecutedResult::class);
    }

    private function getStackAsObject(): RubyClass
    {
        return $this->getStackAsAny(RubyClass::class)
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace());
    }

    private function getStack(): OperandEntry
    {
        $operand = $this->context
            ->vmStack()->pop();

        $this->validateType(
            OperandEntry::class,
            $operand,
        );

        return $operand;
    }

    private function getStackAsAny(string $className): RubyClass|CallInfoEntryInterface|RubyClassInterface|ID|ExecutedResult
    {
        $operand = $this->getStack();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }
}
