<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

class BuiltinDefinemethod implements OperationProcessorInterface
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

        $id = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(OperandEntry::class, $id);

        /**
         * @var StringSymbol $methodNameSymbol
         */
        $methodNameSymbol = $id->operand->object->symbol;

        $newPos = $this->context->programCounter()->increase();

        $iseq = $this->context
            ->instructionSequence()
            ->operations()
            ->get($newPos);

        $this->validateType(OperandEntry::class, $iseq);

        /**
         * @var NumberSymbol $symbol
         */
        $symbol = $iseq->operand->symbol;
        $this->validateType(NumberSymbol::class, $symbol);

        $instructionSequence = $this->context->kernel()->loadInstructionSequence(
            aux: new Aux(
                loader: new AuxLoader(
                    index: $symbol->number,
                ),
            ),
        );

        $instructionSequence->load();

        $this->context->self()->def(
            $methodNameSymbol,
            $instructionSequence,
        );

        return ProcessedStatus::SUCCESS;
    }
}
