<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Float_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinOptPlus implements OperationProcessorInterface
{
    use OperandHelper;
    use OperatorCalculatable;

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
        return $this->processArithmetic('+');
    }

    private function compute(RubyClassInterface $leftOperand, RubyClassInterface $rightOperand): ?RubyClassInterface
    {
        $value = null;
        if ($leftOperand instanceof String_ && $rightOperand instanceof String_) {
            $value = $this->computeStringPlusString($leftOperand, $rightOperand);
        }

        if ($leftOperand instanceof Integer_ && $rightOperand instanceof Integer_) {
            $value = $this->computeNumberPlusNumber($leftOperand, $rightOperand);
        }

        if ($leftOperand instanceof Float_ && $rightOperand instanceof Float_) {
            $value = $this->computeFloatPlusFloat($leftOperand, $rightOperand);
        }

        if ($leftOperand instanceof Array_ && $rightOperand instanceof Array_) {
            $value = $this->computeArrayPlusArray($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeStringPlusString(String_ $leftOperand, String_ $rightOperand): RubyClassInterface
    {
        return String_::createBy(
            $leftOperand . $rightOperand
        );
    }

    private function computeNumberPlusNumber(Integer_ $leftOperand, Integer_ $rightOperand): RubyClassInterface
    {
        return Integer_::createBy(
            $leftOperand->valueOf() + $rightOperand->valueOf()
        );
    }

    private function computeFloatPlusFloat(Float_ $leftOperand, Float_ $rightOperand): RubyClassInterface
    {
        return Float_::createBy(
            $leftOperand->valueOf() + $rightOperand->valueOf()
        );
    }

    private function computeArrayPlusArray(Array_ $leftOperand, Array_ $rightOperand): RubyClassInterface
    {
        return Array_::createBy(
            [
                ...$leftOperand,
                ...$rightOperand,
            ],
        );
    }
}
