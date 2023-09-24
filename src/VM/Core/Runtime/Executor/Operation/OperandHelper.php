<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Entity\Float_;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Entity\Offset;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\ID;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

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
        return $this->getOperandAsAny(ID::class);
    }

    private function getOperandAsMain(): RubyClassInterface
    {
        return $this->getOperandAsAny(RubyClassInterface::class);
    }

    private function getOperandAsCallInfo(): CallInfoInterface
    {
        return $this->getOperandAsAny(CallInfoInterface::class);
    }

    private function getOperandAsExecutedResult(): ExecutedResult
    {
        return $this->getOperandAsAny(ExecutedResult::class);
    }

    private function getOperandAsObject(): RubyClassInterface
    {
        return $this->getOperandAsAny(RubyClassInterface::class)
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
            RubyClass::class
        );

        return $stack->entity;
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

        return $entity;
    }

    private function getStackAsID(): ID
    {
        return $this->getStackAsAny(ID::class);
    }

    private function getStackAsClass(): RubyClassInterface
    {
        return $this->getStackAsAny(RubyClassInterface::class);
    }

    private function getStackAsCallInfo(): CallInfoInterface
    {
        return $this->getStackAsAny(CallInfoInterface::class);
    }

    private function getStackAsExecutedResult(): ExecutedResult
    {
        return $this->getStackAsAny(ExecutedResult::class);
    }

    private function getStackAsObject(): RubyClassInterface
    {
        return $this->getStackAsAny(RubyClassInterface::class)
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
