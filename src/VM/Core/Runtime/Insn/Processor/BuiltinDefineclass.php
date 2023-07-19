<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinDefineclass implements OperationProcessorInterface
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

        /**
         * @var OperandEntry $id
         */
        $idOperand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(
            OperandEntry::class,
            $idOperand,
        );

        $newPos = $this->context->programCounter()->increase();

        /**
         * @var OperandEntry $iseqOperand
         */
        $iseqOperand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);


        $this->validateType(
            OperandEntry::class,
            $iseqOperand,
        );

        /**
         * @var NumberSymbol $iseqNumber
         */
        $iseqNumber = $iseqOperand->operand->symbol;

        /**
         * @var ID $id
         */
        $id = $idOperand->operand;

        $this->validateType(
            ID::class,
            $id,
        );

        $newPos = $this->context->programCounter()->increase();

        /**
         * @var OperandEntry $flagsOperand
         */
        $flagsOperand = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        /**
         * @var NumberSymbol $flags
         */
        $flags = $flagsOperand->operand->symbol;

        $this->validateType(
            NumberSymbol::class,
            $flags,
        );

        var_dump($flags, $iseqNumber, $id);

        throw new OperationProcessorException(
            sprintf(
                'The `%s` (opcode: 0x%02x) processor is not implemented yet',
                strtolower($this->insn->name),
                $this->insn->value,
            )
        );
    }
}
