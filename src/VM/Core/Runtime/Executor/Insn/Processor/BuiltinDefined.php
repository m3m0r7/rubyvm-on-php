<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinDefined implements OperationProcessorInterface
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
        $opType = $this->operandAsNumber();
        $obj = $this->operandAsRubyClass();
        $pushval = $this->operandAsRubyClass();

        // NOTE: We are no needed a stacked val
        $this->stackAsRubyClass();

        $name = (string) $this
            ->context
            ->kernel()
            ->findId($obj->valueOf())
            ->object;

        $result = NilClass::createBy();

        // Looking for in local var
        if ($this->context->self()->context()->environmentTable()->findBy($name) instanceof \RubyVM\VM\Core\Runtime\Essential\RubyClassInterface) {
            $result = String_::createBy('local-variable');
        }

        // Looking for a method
        if ($result instanceof NilClass && $this->context->self()->userlandHeapSpace()->userlandMethods()->has($name)) {
            $result = String_::createBy('method');
        }

        // Looking for a class
        if ($result instanceof NilClass && $this->context->self()->userlandHeapSpace()->userlandClasses()->has($name)) {
            $result = String_::createBy('constant');
        }

        // Looking for a global
        if ($result instanceof NilClass && $this->context->self()->userlandHeapSpace()->userlandInstanceVariables()->has($name)) {
            $result = String_::createBy('global-variable');
        }

        $this->context->vmStack()->push(new Operand($result));

        return ProcessedStatus::SUCCESS;
    }
}
