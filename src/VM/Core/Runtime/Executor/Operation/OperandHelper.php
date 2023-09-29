<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Entity\Float_;
use RubyVM\VM\Core\Runtime\Entity\Nil;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Entity\Offset;
use RubyVM\VM\Core\Runtime\Entity\RegExp;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Entity\Symbol;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\ID;

/**
 * This trait helps an IDE.
 */
trait OperandHelper
{
    use Validatable;

    private function getOperandAsEntity(): EntityInterface
    {
        $operand = $this->getOperandAsAny(
            RubyClass::class,
        );

        assert($operand instanceof RubyClass);

        return $operand->entity;
    }

    private function getOperandAsNumber(): Number
    {
        /**
         * @var Number $number
         */
        $number = $this->getOperandAsEntity();

        $this->validateType(
            Number::class,
            $number,
        );

        return $number;
    }

    private function getOperandAsString(): String_
    {
        /**
         * @var String_ $entity
         */
        $entity = $this->getOperandAsEntity();

        $this->validateType(
            String_::class,
            $entity,
        );

        return $entity;
    }

    private function getOperandAsFloat(): Float_
    {
        /**
         * @var Float_ $entity
         */
        $entity = $this->getOperandAsEntity();

        $this->validateType(
            Float_::class,
            $entity,
        );

        return $entity;
    }

    private function getOperandAsOffset(): Offset
    {
        /**
         * @var Offset $entity
         */
        $entity = $this->getOperandAsEntity();

        $this->validateType(
            Offset::class,
            $entity,
        );

        return $entity;
    }

    private function getOperandAsArray(): Array_
    {
        /**
         * @var Array_ $entity
         */
        $entity = $this->getOperandAsEntity();

        $this->validateType(
            Array_::class,
            $entity,
        );

        return $entity;
    }

    private function getOperandAsID(): ID
    {
        $value = $this->getOperandAsAny(ID::class);
        assert($value instanceof ID);

        return $value;
    }

    private function getOperandAsRubyClass(): RubyClassInterface
    {
        $value = $this->getOperandAsAny(RubyClassInterface::class);
        assert($value instanceof RubyClassInterface);

        return $value;
    }

    private function getOperandAsCallInfo(): CallInfoInterface
    {
        $value = $this->getOperandAsAny(CallInfoInterface::class);
        assert($value instanceof CallInfoInterface);

        return $value;
    }

    private function getOperandAsExecutedResult(): ExecutedResult
    {
        $value = $this->getOperandAsAny(ExecutedResult::class);
        assert($value instanceof ExecutedResult);

        return $value;
    }

    private function getOperandAsObject(): RubyClassInterface
    {
        return $this->getOperandAsRubyClass()
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace());
    }

    private function getOperand(): Operand
    {
        /**
         * @var Operand $operand
         */
        $operand = $this->context
            ->instructionSequence()
            ->body()
            ->info()
            ->operationEntries()
            ->get($this->context->programCounter()->increase());

        $this->validateType(
            Operand::class,
            $operand,
        );

        return $operand;
    }

    private function getOperandAsAny(string $className): CallInfoInterface|RubyClassInterface|ID|ExecutedResult
    {
        $operand = $this->getOperand();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }

    private function getStackAsEntity(): EntityInterface
    {
        $stack = $this->getStackAsAny(
            RubyClassInterface::class
        );

        assert($stack instanceof RubyClassInterface);

        return $stack->entity();
    }

    private function getStackAsSymbol(): Symbol
    {
        /**
         * @var Symbol $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            Symbol::class,
            $entity,
        );

        return $entity;
    }

    private function getStackAsNumber(): Number
    {
        /**
         * @var Number $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            Number::class,
            $entity,
        );

        return $entity;
    }

    private function getStackAsArray(): Array_
    {
        /**
         * @var Array_ $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            Array_::class,
            $entity,
        );

        return $entity;
    }

    private function getStackAsString(): String_
    {
        /**
         * @var String_ $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            String_::class,
            $entity,
        );

        assert($entity instanceof String_);

        return $entity;
    }

    private function getStackAsStringOrNil(): String_|Nil
    {
        $entity = $this->getStackAsEntity();

        assert($entity instanceof String_ || $entity instanceof Nil);

        return $entity;
    }

    private function getStackAsRegExp(): RegExp
    {
        /**
         * @var RegExp $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            RegExp::class,
            $entity,
        );

        assert($entity instanceof RegExp);

        return $entity;
    }

    private function getStackAsFloat(): Float_
    {
        /**
         * @var Float_ $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            Float_::class,
            $entity,
        );

        assert($entity instanceof Float_);

        return $entity;
    }

    private function getStackAsOffsetSymbol(): Offset
    {
        /**
         * @var Offset $entity
         */
        $entity = $this->getStackAsEntity();

        $this->validateType(
            Offset::class,
            $entity,
        );

        assert($entity instanceof Offset);

        return $entity;
    }

    private function getStackAsID(): ID
    {
        $value = $this->getStackAsAny(ID::class);

        assert($value instanceof ID);

        return $value;
    }

    private function getStackAsRubyClass(): RubyClassInterface
    {
        $value = $this->getStackAsAny(RubyClassInterface::class);
        assert($value instanceof RubyClassInterface);

        return $value;
    }

    private function getStackAsCallInfo(): CallInfoInterface
    {
        $value = $this->getStackAsAny(CallInfoInterface::class);
        assert($value instanceof CallInfoInterface);

        return $value;
    }

    private function getStackAsExecutedResult(): ExecutedResult
    {
        $value = $this->getStackAsAny(ExecutedResult::class);
        assert($value instanceof ExecutedResult);

        return $value;
    }

    private function getStackAsObject(): RubyClassInterface
    {
        return $this->getStackAsRubyClass()
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace());
    }

    private function getStack(): Operand
    {
        $operand = $this->context
            ->vmStack()->pop();

        $this->validateType(
            Operand::class,
            $operand,
        );

        return $operand;
    }

    private function getStackAsAny(string $className): CallInfoInterface|RubyClassInterface|ID|ExecutedResult
    {
        $operand = $this->getStack();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }
}
