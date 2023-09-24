<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait OperatorCalculatable
{
    use Validatable;
    use OperandHelper;

    private function processArithmetic(string $expectedOperator): ProcessedStatus
    {
        $recv = $this->context->vmStack()->pop();
        $obj = $this->context->vmStack()->pop();

        $this->validateType(Operand::class, $recv);
        $this->validateType(Operand::class, $obj);

        $callDataOperand = $this->getOperandAsCallInfo();

        $operator = $callDataOperand->callData
            ->mid
            ->object;

        $leftOperand = $obj->operand->entity->symbol();
        $rightOperand = $recv->operand->entity->symbol();

        $value = null;
        if ($operator instanceof StringSymbol) {
            if ((string) $operator !== $expectedOperator) {
                throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process %s operator because string concatenating was allowed only `%s`', strtolower($this->insn->name), $this->insn->value, $operator, $expectedOperator));
            }
            $value = $this->compute($leftOperand, $rightOperand);
        }

        if ($value === null) {
            throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process `%s` operator because it was not implemented or cannot comparison operator %s and %s', strtolower($this->insn->name), $this->insn->value, $operator, ClassHelper::nameBy($leftOperand), ClassHelper::nameBy($rightOperand)));
        }

        $this->context->vmStack()->push(new Operand($value));

        return ProcessedStatus::SUCCESS;
    }

    abstract private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?RubyClassInterface;
}
