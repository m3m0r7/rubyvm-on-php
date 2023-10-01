<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

trait OperatorCalculatable
{
    use Validatable;
    use OperandHelper;

    private function processArithmetic(string $expectedOperator): ProcessedStatus
    {
        $recv = $this->getStackAsRubyClass();
        $obj = $this->getStackAsRubyClass();

        $callDataOperand = $this->getOperandAsCallInfo();

        $operator = $callDataOperand
            ->callData()
            ->mid()
            ->object;

        $value = null;
        if ($operator instanceof StringSymbol || $operator instanceof SymbolSymbol) {
            if ((string) $operator !== $expectedOperator) {
                throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process %s operator because string concatenating was allowed only `%s`', strtolower((string) $this->insn->name), $this->insn->value, $operator, $expectedOperator));
            }

            $value = $obj
                ->setRuntimeContext($this->context)
                ->{$expectedOperator}($callDataOperand, $recv);
        }

        if ($value === null) {
            throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process `%s` operator because it was not implemented or cannot comparison operator %s and %s', strtolower((string) $this->insn->name), $this->insn->value, $operator, ClassHelper::nameBy($recv), ClassHelper::nameBy($obj)));
        }

        $this->context->vmStack()->push(new Operand($value));

        return ProcessedStatus::SUCCESS;
    }
}
