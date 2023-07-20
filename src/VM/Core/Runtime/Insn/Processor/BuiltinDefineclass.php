<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;

class BuiltinDefineclass implements OperationProcessorInterface
{
    use OperandHelper;
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
        /**
         * @var StringSymbol $className
         */
        $className = $this->getOperandAndValidateID()->object->symbol;
        $iseqNumber = $this->getOperandAndValidateNumberSymbol();
        $flags = $this->getOperandAndValidateNumberSymbol();

        $instructionSequence = $this->context->kernel()->loadInstructionSequence(
            aux: new Aux(
                loader: new AuxLoader(
                    index: $iseqNumber->number,
                ),
            ),
        );

        $instructionSequence->load();


        $executor = (new Executor(
            kernel: $this->context->kernel(),
            main: $this->context->self(),
            operationProcessorEntries: $this->context->operationProcessorEntries(),
            instructionSequence: $instructionSequence,
            logger: $this->context->logger(),
            debugger: $this->context->debugger(),
            previousContext: $this->context,
        ));

        $this->context
            ->self()
            ->class(
                $flags,
                $className,
                $executor->context(),
            );

        return ProcessedStatus::SUCCESS;
    }
}
