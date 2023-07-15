<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\ObjectInfo;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolType;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinOptPlus implements OperationProcessorInterface
{
    use Validatable;

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
            if ((string) $operator !== '+') {
                throw new OperationProcessorException(
                    sprintf(
                        'The `%s` (opcode: 0x%02x) processor cannot process %s operator because string concatenating was allowed only `+`',
                        strtolower($this->insn->name),
                        $this->insn->value,
                        $operator,
                    )
                );
            }

            if ($leftOperand instanceof StringSymbol && $rightOperand instanceof StringSymbol) {
                $value = $this->calculateStringPlusString($leftOperand, $rightOperand);
            }
            if ($leftOperand instanceof NumberSymbol && $rightOperand instanceof NumberSymbol) {
                $value = $this->calculateNumberPlusNumber($leftOperand, $rightOperand);
            }
        }

        if ($value === null) {
            throw new OperationProcessorException(
                sprintf(
                    'The `%s` (opcode: 0x%02x) processor cannot process %s operator because it was not implemented or unknown operator',
                    strtolower($this->insn->name),
                    $this->insn->value,
                    $operator,
                )
            );
        }

        $this->context->vmStack()->push(new OperandEntry($value));

        return ProcessedStatus::SUCCESS;
    }

    private function calculateStringPlusString(StringSymbol $leftOperand, StringSymbol $rightOperand): Object_
    {
        return new Object_(
            new ObjectInfo(
                SymbolType::STRING,
                0,
                1,
                0
            ),
            new StringSymbol(
                $leftOperand . $rightOperand
            ),
        );
    }
    private function calculateNumberPlusNumber(NumberSymbol $leftOperand, NumberSymbol $rightOperand): Object_
    {
        return new Object_(
            new ObjectInfo(
                SymbolType::FIXNUM,
                0,
                1,
                0
            ),
            new NumberSymbol(
                $leftOperand->number + $rightOperand->number
            ),
        );
    }
}
