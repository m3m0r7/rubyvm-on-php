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
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\SymbolInterface;

class BuiltinOptLe implements OperationProcessorInterface
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
        return $this->processArithmetic('<=');
    }

    private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->computeNumberLessThanOrEqualsNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->computeFloatLessThanOrEqualsFloat($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeNumberLessThanOrEqualsNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->valueOf() <= $rightOperand->valueOf()
        ))->toObject();
    }

    private function computeFloatLessThanOrEqualsFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->valueOf() <= $rightOperand->valueOf()
        ))->toObject();
    }
}
