<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Float_;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

class BuiltinOptDiv implements OperationProcessorInterface
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
        return $this->processArithmetic('/');
    }

    private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?RubyClass
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->computeNumberDivideNumber($leftOperand, $rightOperand);
        }
        if ($leftOperand instanceof FloatSymbol && $rightOperand instanceof FloatSymbol) {
            $value = $this->computeFloatDivideFloat($leftOperand, $rightOperand);
        }

        return $value;
    }

    private function computeNumberDivideNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): RubyClassInterface
    {
        return (new Number(new NumberSymbol(
            (int) ($leftOperand->valueOf() / $rightOperand->valueOf())
        )))->toRubyClass();
    }

    private function computeFloatDivideFloat(FloatSymbol $leftOperand, FloatSymbol $rightOperand): RubyClassInterface
    {
        return (new Float_(new FloatSymbol(
            $leftOperand->valueOf() / $rightOperand->valueOf()
        )))->toRubyClass();
    }
}
