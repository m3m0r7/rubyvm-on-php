<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinOptNilP implements OperationProcessorInterface
{
    use OperandHelper;
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
        $class = $this->stackAsRubyClass();
        $callInfo = $this->operandAsCallInfo();

        $methodName = (string) $callInfo
            ->callData()
            ->mid()
            ->object;

        /**
         * @var null|RubyClassInterface $result
         */
        $result = $class
            ->setRuntimeContext($this->context)
            ->{$methodName}($callInfo);

        if ($result === null) {
            $result = NilClass::createBy();
        }

        $this->context->vmStack()->push(
            new Operand(
                $result,
            ),
        );

        return ProcessedStatus::SUCCESS;
    }
}
