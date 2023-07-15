<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Helper\OperatorCalculatable;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;

class BuiltinOptMinus implements OperationProcessorInterface
{
    use OperatorCalculatable {
        process as processArithmetic;
    }

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
        return $this->processArithmetic('-');
    }

    private function calculate(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_
    {
        $value = null;
        if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
            $value = $this->calculateNumberMinusNumber($leftOperand, $rightOperand);
        }
        return $value;
    }

    private function calculateNumberMinusNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return new Object_(
            new ObjectInfo(
                SymbolType::FIXNUM,
                0,
                1,
                0
            ),
            new NumberSymbol(
                $leftOperand->number - $rightOperand->number
            ),
        );
    }
}
