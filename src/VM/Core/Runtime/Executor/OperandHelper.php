<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\FloatSymbol;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StructSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

/**
 * TODO: implement this
 * This trait helps an IDE
 */
trait OperandHelper
{
    use Validatable;

    private function getOperandAndValidateSymbol(): SymbolInterface
    {

        $operand = $this->getOperandAndValidateAny(
            Object_::class
        );

        return $operand->symbol;
    }

    private function getOperandAndValidateNumberSymbol(): NumberSymbol
    {
        /**
         * @var NumberSymbol $symbol
         */
        $symbol = $this->getOperandAndValidateSymbol();

        $this->validateType(
            NumberSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAndValidateStructSymbol(): StructSymbol
    {
        /**
         * @var StructSymbol $symbol
         */
        $symbol = $this->getOperandAndValidateSymbol();

        $this->validateType(
            StructSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAndValidateStringSymbol(): StringSymbol
    {
        /**
         * @var StringSymbol $symbol
         */
        $symbol = $this->getOperandAndValidateSymbol();

        $this->validateType(
            StringSymbol::class,
            $symbol,
        );

        return $symbol;
    }

    private function getOperandAndValidateFloatSymbol(): FloatSymbol
    {
        /**
         * @var FloatSymbol $symbol
         */
        $symbol = $this->getOperandAndValidateSymbol();

        $this->validateType(
            FloatSymbol::class,
            $symbol,
        );
    }

    private function getOperandAndValidateID(): ID
    {
        return $this->getOperandAndValidateAny(ID::class);
    }

    private function getOperandAndValidateMain(): MainInterface
    {
        return $this->getOperandAndValidateAny(MainInterface::class);
    }

    private function getOperandAndValidateCallInfo(): CallInfoEntryInterface
    {
        return $this->getOperandAndValidateAny(CallInfoEntryInterface::class);
    }

    private function getOperandAndValidateExecutedResult(): ExecutedResult
    {
        return $this->getOperandAndValidateAny(ExecutedResult::class);
    }

    private function getOperandAndValidateObject(): Object_
    {
        return $this->getOperandAndValidateAny(
            Object_::class
        );
    }

    private function getOperandAndValidateAny(string $className): Object_|CallInfoEntryInterface|MainInterface|ID|ExecutedResult
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

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }
}
