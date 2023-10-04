<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Float_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Symbol;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Offset;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Regexp;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\ID;

/**
 * This trait helps an IDE.
 */
trait OperandHelper
{
    use Validatable;

    private function operandAsRubyClass(): RubyClassInterface
    {
        $operand = $this->operandAsAny(
            RubyClassInterface::class,
        );

        assert($operand instanceof RubyClassInterface);

        return $operand;
    }

    private function operandAsNumber(): Integer_
    {
        /**
         * @var Integer_ $number
         */
        $number = $this->operandAsRubyClass();

        $this->validateType(
            Integer_::class,
            $number,
        );

        return $number;
    }

    private function operandAsString(): String_
    {
        /**
         * @var String_ $entity
         */
        $entity = $this->operandAsRubyClass();

        $this->validateType(
            String_::class,
            $entity,
        );

        return $entity;
    }

    private function operandAsFloat(): Float_
    {
        /**
         * @var Float_ $entity
         */
        $entity = $this->operandAsRubyClass();

        $this->validateType(
            Float_::class,
            $entity,
        );

        return $entity;
    }

    private function operandAsOffset(): Offset
    {
        /**
         * @var Offset $entity
         */
        $entity = $this->operandAsRubyClass();

        $this->validateType(
            Offset::class,
            $entity,
        );

        return $entity;
    }

    private function operandAsArray(): Array_
    {
        /**
         * @var Array_ $entity
         */
        $entity = $this->operandAsRubyClass();

        $this->validateType(
            Array_::class,
            $entity,
        );

        return $entity;
    }

    private function operandAsID(): ID
    {
        $value = $this->operandAsAny(ID::class);
        assert($value instanceof ID);

        return $value;
    }

    private function operandAsCallInfo(): CallInfoInterface
    {
        $value = $this->operandAsAny(CallInfoInterface::class);
        assert($value instanceof CallInfoInterface);

        return $value;
    }

    private function operandAsExecutedResult(): ExecutedResult
    {
        $value = $this->operandAsAny(ExecutedResult::class);
        assert($value instanceof ExecutedResult);

        return $value;
    }

    private function operandAsObject(): RubyClassInterface
    {
        return $this->operandAsRubyClass()
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace());
    }

    private function operand(): Operand
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

    private function operandAsAny(string $className): CallInfoInterface|RubyClassInterface|ID|ExecutedResult|ContextInterface
    {
        $operand = $this->operand();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }

    private function stackAsRubyClass(): RubyClassInterface
    {
        $stack = $this->stackAsAny(
            RubyClassInterface::class
        );

        assert($stack instanceof RubyClassInterface);

        return $stack;
    }

    private function stackAsSymbol(): Symbol
    {
        /**
         * @var Symbol $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Symbol::class,
            $entity,
        );

        return $entity;
    }

    private function stackAsNumber(): Integer_
    {
        /**
         * @var Integer_ $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Integer_::class,
            $entity,
        );

        return $entity;
    }

    private function stackAsArray(): Array_
    {
        /**
         * @var Array_ $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Array_::class,
            $entity,
        );

        return $entity;
    }

    private function stackAsString(): String_
    {
        /**
         * @var String_ $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            String_::class,
            $entity,
        );

        assert($entity instanceof String_);

        return $entity;
    }

    private function stackAsStringOrNil(): String_|NilClass
    {
        $entity = $this->stackAsRubyClass();

        assert($entity instanceof String_ || $entity instanceof NilClass);

        return $entity;
    }

    private function stackAsRegExp(): Regexp
    {
        /**
         * @var Regexp $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Regexp::class,
            $entity,
        );

        assert($entity instanceof Regexp);

        return $entity;
    }

    private function stackAsFloat(): Float_
    {
        /**
         * @var Float_ $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Float_::class,
            $entity,
        );

        assert($entity instanceof Float_);

        return $entity;
    }

    private function stackAsOffsetSymbol(): Offset
    {
        /**
         * @var Offset $entity
         */
        $entity = $this->stackAsRubyClass();

        $this->validateType(
            Offset::class,
            $entity,
        );

        assert($entity instanceof Offset);

        return $entity;
    }

    private function stackAsID(): ID
    {
        $value = $this->stackAsAny(ID::class);

        assert($value instanceof ID);

        return $value;
    }

    private function stackAsCallInfo(): CallInfoInterface
    {
        $value = $this->stackAsAny(CallInfoInterface::class);
        assert($value instanceof CallInfoInterface);

        return $value;
    }

    private function stackAsExecutedResult(): ExecutedResult
    {
        $value = $this->stackAsAny(ExecutedResult::class);
        assert($value instanceof ExecutedResult);

        return $value;
    }

    private function stackAsObject(): RubyClassInterface
    {
        return $this->stackAsRubyClass()
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

    private function stackAsAny(string $className): CallInfoInterface|RubyClassInterface|ID|ExecutedResult|ContextInterface
    {
        $operand = $this->getStack();

        $this->validateType(
            $className,
            $operand->operand,
        );

        return $operand->operand;
    }
}
