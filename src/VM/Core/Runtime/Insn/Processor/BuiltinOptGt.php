<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Helper\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\FloatSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinOptGt implements OperationProcessorInterface
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

    public function process(SymbolInterface|ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        return $this->processArithmetic('>');
    }

    private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->computeNumberGreaterThanOrEqualsNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->computeFloatGreaterThanOrEqualsFloat($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeNumberGreaterThanOrEqualsNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->valueOf() > $rightOperand->valueOf()
        ))->toObject();
    }

    private function computeFloatGreaterThanOrEqualsFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->valueOf() > $rightOperand->valueOf()
        ))->toObject();
    }
}
