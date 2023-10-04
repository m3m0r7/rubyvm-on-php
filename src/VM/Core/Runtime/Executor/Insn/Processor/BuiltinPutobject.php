<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;

class BuiltinPutobject implements OperationProcessorInterface
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

    public function process(): ProcessedStatus
    {
        $operand = $this->operandAsRubyClass();
        $operand
            ->setUserlandHeapSpace(
                $operand
                    ->setRuntimeContext($this->context)
                    ->context()
                    ->self()
                    ->userlandHeapSpace()
                    ->userlandClasses()
                    ->get($operand->className()),
            )
            ->setRuntimeContext($this->context);

        $this->context->vmStack()->push(new Operand($operand));

        return ProcessedStatus::SUCCESS;
    }
}
