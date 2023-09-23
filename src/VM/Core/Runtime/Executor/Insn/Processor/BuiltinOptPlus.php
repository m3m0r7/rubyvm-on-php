<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

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

    private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?RubyClass
    {
        $value = null;
        if ($leftOperand instanceof StringSymbol && $rightOperand instanceof StringSymbol) {
            $value = $this->computeStringPlusString($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->computeNumberPlusNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->computeFloatPlusFloat($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof ArraySymbol && $rightOperand instanceof ArraySymbol) {
            $value = $this->computeArrayPlusArray($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeStringPlusString(StringSymbol $leftOperand, StringSymbol $rightOperand): RubyClass
    {
        return (new StringSymbol(
            $leftOperand . $rightOperand
        ))->toObject();
    }

    private function computeNumberPlusNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): RubyClass
    {
        return (new NumberSymbol(
            $leftOperand->valueOf() + $rightOperand->valueOf()
        ))->toObject();
    }

    private function computeFloatPlusFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): RubyClass
    {
        return (new FloatSymbol(
            $leftOperand->valueOf() + $rightOperand->valueOf()
        ))->toObject();
    }

    private function computeArrayPlusArray(ArraySymbol $leftOperand, ArraySymbol $rightOperand): RubyClass
    {
        return (new ArraySymbol(
            [
                ...$leftOperand,
                ...$rightOperand,
            ],
        ))->toObject();
    }
}
