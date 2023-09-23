<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Object_;
use RubyVM\VM\Core\YARV\Criterion\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\SymbolInterface;

class BuiltinOptMinus implements OperationProcessorInterface
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
        return $this->processArithmetic('-');
    }

    private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->computeNumberMinusNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->computeFloatMinusFloat($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeNumberMinusNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return (new NumberSymbol(
            $leftOperand->valueOf() - $rightOperand->valueOf()
        ))->toObject();
    }

    private function computeFloatMinusFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): Object_
    {
        return (new FloatSymbol(
            $leftOperand->valueOf() - $rightOperand->valueOf()
        ))->toObject();
    }
}
