<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;

class BuiltinOptCaseDispatch implements OperationProcessorInterface
{
    use OperandHelper;
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
        $hash = $this->operandAsID();
        $elseOffset = $this->operandAsRubyClass();
        $key = $this->stackAsRubyClass();

        if ($key->valueOf() !== $hash->object->valueOf()) {
            $this->context
                ->programCounter()
                ->set($elseOffset->valueOf());
        }

        return ProcessedStatus::SUCCESS;
    }
}
