<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait OperatorCalculatable
{
    use Validatable;
    use OperandHelper;

    private function processArithmetic(string $expectedOperator): ProcessedStatus
    {
        $recv = $this->context->vmStack()->pop();
        $obj = $this->context->vmStack()->pop();

        $this->validateType(OperandEntry::class, $recv);
        $this->validateType(OperandEntry::class, $obj);

        $callDataOperand = $this->getOperandAsCallInfo();

        /**
         * @var SymbolInterface $operator
         */
        $operator = $callDataOperand->callData
            ->mid
            ->object
            ->symbol
        ;

        /**
         * @var SymbolInterface $leftOperand
         */
        $leftOperand = $obj->operand->symbol;

        /**
         * @var SymbolInterface $rightOperand
         */
        $rightOperand = $recv->operand->symbol;

        $value = null;
        if ($operator instanceof StringSymbol) {
            if ((string) $operator !== $expectedOperator) {
                throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process %s operator because string concatenating was allowed only `%s`', strtolower($this->insn->name), $this->insn->value, $operator, $expectedOperator));
            }
            $value = $this->compute($leftOperand, $rightOperand);
        }

        if (null === $value) {
            throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor cannot process `%s` operator because it was not implemented or cannot comparison operator %s and %s', strtolower($this->insn->name), $this->insn->value, $operator, ClassHelper::nameBy($leftOperand), ClassHelper::nameBy($rightOperand)));
        }

        $this->context->vmStack()->push(new OperandEntry($value));

        return ProcessedStatus::SUCCESS;
    }

    abstract private function compute(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_;
}
