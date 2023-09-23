<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class BuiltinDefinemethod implements OperationProcessorInterface
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

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        /**
         * @var StringSymbol $methodNameSymbol
         */
        $methodNameSymbol = $this->getOperandAsID()
            ->object
            ->symbol;

        $instructionSequence = $this->context->kernel()
            ->loadInstructionSequence(
                aux: new Aux(
                    loader: new AuxLoader(
                        index: $this->getOperandAsNumberSymbol()
                            ->valueOf(),
                    ),
                ),
            );

        $instructionSequence->load();

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $this->context->self(),
            instructionSequence: $instructionSequence,
            option: $this->context->option(),
            debugger: $this->context->debugger(),
            previousContext: $this->context,
        ));

        $executor->context()
            ->appendTrace($methodNameSymbol->valueOf());

        $this->context
            ->self()
            ->def(
                $methodNameSymbol,
                $executor->context(),
            );

        return ProcessedStatus::SUCCESS;
    }
}
