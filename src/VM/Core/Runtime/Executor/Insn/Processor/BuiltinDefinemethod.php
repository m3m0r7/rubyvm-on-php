<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Symbol;
use RubyVM\VM\Core\Runtime\ClassCreator;
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
        $methodName = ClassCreator::createClassBySymbol(
            $this->getOperandAsID()
                ->object
        );

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
            ->appendTrace($methodName->valueOf());

        $class = $this->context->self();

        $receiverClass = $this
            ->context
            ->self();

        if ($class->valueOf() === 'singletonclass') {
            $receiverClass = $receiverClass
                ->context()
                ->self();
            $context = $receiverClass->context();
        } else {
            $context = $this->context;
        }

        $executor = new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $receiverClass,
            instructionSequence: $instructionSequence,
            option: $this->context->option(),
            debugger: $this->context->debugger(),
            parentContext: $context,
        );

        assert($methodName instanceof Symbol);

        $receiverClass
            ->def(
                $methodName,
                $executor->context(),
            );

        return ProcessedStatus::SUCCESS;
    }
}
