<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Entity\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityHelper;
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
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

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

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        $className = EntityHelper::createEntityBySymbol($this->getOperandAsID()->object)
            ->toBeRubyClass();
        $iseqNumber = $this->getOperandAsNumber();
        $flags = $this->getOperandAsNumber();

        $instructionSequence = $this->context->kernel()
            ->loadInstructionSequence(
                aux: new Aux(
                    loader: new AuxLoader(
                        index: $iseqNumber->valueOf(),
                    ),
                ),
            );

        $instructionSequence->load();

        /**
         * @var StringSymbol $classNameSymbol
         */
        $classNameSymbol = $className->entity()->symbol();
        $class = Class_::of($classNameSymbol, $this->context);

        /**
         * @var NumberSymbol $flagNumber
         */
        $flagNumber = $flags->symbol();

        $this->context
            ->self()
            ->class(
                $flagNumber,
                $classNameSymbol,
            );

        $class
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->context->self()->userlandHeapSpace()->userlandClasses()->get((string) $className));

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $class,
            instructionSequence: $instructionSequence,
            option: $this->context->option(),
            debugger: $this->context->debugger(),
            previousContext: $this->context,
        ));

        $executor->context()
            ->renewEnvironmentTable();

        $executor->context()
            ->appendTrace($class->entity()->valueOf());

        $result = $executor->execute();

        if ($result->threw instanceof \Throwable) {
            throw $result->threw;
        }

        return ProcessedStatus::SUCCESS;
    }
}
