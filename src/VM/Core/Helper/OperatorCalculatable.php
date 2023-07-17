<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait OperatorCalculatable
{
    use Validatable;

    private function processArithmetic(string $expectedOperator): ProcessedStatus
    {
        $newPos = $this->context->programCounter()->increase();

        $callDataOperand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(OperandEntry::class, $callDataOperand);

        $recv = $this->context->vmStack()->pop();
        $obj = $this->context->vmStack()->pop();

        $this->validateType(OperandEntry::class, $recv);
        $this->validateType(OperandEntry::class, $obj);

        /**
         * @var SymbolInterface $operator
         */
        $operator = $callDataOperand->operand
            ->callData
            ->mid
            ->object
            ->symbol;

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
                throw new OperationProcessorException(
                    sprintf(
                        'The `%s` (opcode: 0x%02x) processor cannot process %s operator because string concatenating was allowed only `%s`',
                        strtolower($this->insn->name),
                        $this->insn->value,
                        $operator,
                        $expectedOperator,
                    )
                );
            }
            $value = $this->calculate($leftOperand, $rightOperand);
        }

        if ($value === null) {
            throw new OperationProcessorException(
                sprintf(
                    'The `%s` (opcode: 0x%02x) processor cannot process `%s` operator because it was not implemented or cannot comparison operator',
                    strtolower($this->insn->name),
                    $this->insn->value,
                    $operator,
                )
            );
        }

        $this->context->vmStack()->push(new OperandEntry($value));

        return ProcessedStatus::SUCCESS;
    }

    abstract private function calculate(SymbolInterface $leftOperand, SymbolInterface $rightOperand): ?Object_;
}
