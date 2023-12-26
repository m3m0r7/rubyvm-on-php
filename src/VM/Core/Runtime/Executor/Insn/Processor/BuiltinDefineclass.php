<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Symbol;
use RubyVM\VM\Core\Runtime\ClassCreator;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\Aux\AuxLoader;

class BuiltinDefineclass implements OperationProcessorInterface
{
    use OperandHelper;
    use Validatable;

    private InsnInterface $insn;

    private ContextInterface $context;

    public function prepare(InsnInterface $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void {}

    public function after(): void {}

    public function process(): ProcessedStatus
    {
        $className = ClassCreator::createClassBySymbol($this->operandAsID()->object);
        $iseqNumber = $this->operandAsNumber();
        $flags = $this->operandAsNumber();

        $instructionSequence = $this->context->kernel()
            ->loadInstructionSequence(
                aux: new Aux(
                    loader: new AuxLoader(
                        index: $iseqNumber->valueOf(),
                    ),
                ),
            );

        $instructionSequence->load();

        assert($className instanceof Symbol);

        $class = Class_::of($className, $this->context);

        $this->context
            ->self()
            ->class(
                $flags,
                $className,
            );

        $class
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace()->userlandClasses()->get((string) $className));

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $class,
            instructionSequence: $instructionSequence,
            option: $this->context->option(),
            parentContext: $this->context,
        ));

        $executor->context()
            ->renewEnvironmentTable();

        $executor->context()
            ->appendTrace($class->valueOf());

        $result = $executor->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return ProcessedStatus::SUCCESS;
    }
}
