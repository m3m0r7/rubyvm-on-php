<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Helper\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\BooleanSymbol;
use RubyVM\VM\Core\Runtime\Symbol\FloatSymbol;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptGt implements OperationProcessorInterface
{
    use OperatorCalculatable;

    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(): ProcessedStatus
    {
        return $this->processArithmetic('>');
    }

    private function calculate(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->calculateNumberGreaterThanOrEqualsNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->calculateFloatGreaterThanOrEqualsFloat($leftOperand, $rightOperand);
        }
        return $value;
    }

    private function calculateNumberGreaterThanOrEqualsNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->number > $rightOperand->number
        ))->toObject();
    }

    private function calculateFloatGreaterThanOrEqualsFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): Object_
    {
        return (new BooleanSymbol(
            $leftOperand->number > $rightOperand->number
        ))->toObject();
    }
}
