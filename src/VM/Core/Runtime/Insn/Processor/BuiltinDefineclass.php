<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\UserlandHeapSpace;

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

    public function before(): void {}

    public function after(): void {}

    public function process(SymbolInterface|ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        /**
         * @var StringSymbol $className
         */
        $className = $this->getOperandAsID()->object->symbol;
        $iseqNumber = $this->getOperandAsNumberSymbol();
        $flags = $this->getOperandAsNumberSymbol();

        $instructionSequence = $this->context->kernel()->loadInstructionSequence(
            aux: new Aux(
                loader: new AuxLoader(
                    index: $iseqNumber->valueOf(),
                ),
            ),
        );

        $instructionSequence->load();

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $this->context->self(),
            instructionSequence: $instructionSequence,
            logger: $this->context->logger(),
            userlandHeapSpace: new UserlandHeapSpace(),
            debugger: $this->context->debugger(),
            previousContext: $this->context->renewEnvironmentTable(),
        ));

        $executor->context()
            ->appendTrace($className->valueOf());

        $this->context
            ->self()
            ->class(
                $flags,
                $className,
                $executor,
            );

        return ProcessedStatus::SUCCESS;
    }
}
