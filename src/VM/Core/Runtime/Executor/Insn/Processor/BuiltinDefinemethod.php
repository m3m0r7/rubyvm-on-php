<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Class_;
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
            ->object;

        $instructionSequence = $this->context->kernel()
            ->loadInstructionSequence(
                aux: new Aux(
                    loader: new AuxLoader(
                        index: $this->getOperandAsNumber()
                            ->valueOf(),
                    ),
                ),
            );

        $instructionSequence->load();

        $this->context
            ->appendTrace($methodNameSymbol->valueOf());

        $class = $this->context->self()->entity();

        if ($class->valueOf() === 'singletonclass') {
            $receiverClass = $this
                ->context
                ->self()
                ->context()
                ->self();

            /**
             * @var StringSymbol $symbol
             */
            $symbol = $receiverClass->entity()->symbol();

            $context = $this->context;

            $receiverClass = Class_::of($symbol);
        } else {
            $receiverClass = $this->context
                ->self();

            $executor = new Executor(
                kernel: $this->context->kernel(),
                rubyClass: $this->context->self(),
                instructionSequence: $instructionSequence,
                option: $this->context->option(),
                debugger: $this->context->debugger(),
                previousContext: $this->context,
            );

            $context = $executor->context();
        }

        $receiverClass
            ->def(
                $methodNameSymbol,
                $context,
            );

        return ProcessedStatus::SUCCESS;
    }
}
