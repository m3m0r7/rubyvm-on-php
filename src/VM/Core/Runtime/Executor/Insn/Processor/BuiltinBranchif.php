<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\InsnInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Validatable;

class BuiltinBranchif implements OperationProcessorInterface
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
        $offset = $this->operandAsOffset();

        $entity = $this->stackAsRubyClass();

        if ($entity->testValue()) {
            $this->context
                ->programCounter()
                ->increase($offset->valueOf());

            return ProcessedStatus::JUMPED;
        }

        return ProcessedStatus::SUCCESS;
    }
}
