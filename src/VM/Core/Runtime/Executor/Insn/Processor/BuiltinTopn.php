<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinTopn implements OperationProcessorInterface
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
        $n = $this->operandAsNumber();
        $stacks = [];

        // Get to specified value
        for ($i = 0; $i <= $n->valueOf(); ++$i) {
            $stacks[] = $this->getStack();
        }

        // Re-push same value
        $this->context->vmStack()->push(
            $first = array_pop($stacks) ?? throw new OperationProcessorException(
                'VMStack is null',
            ),
        );

        // Re-push already existed stacks
        foreach ($stacks as $stack) {
            $this->context->vmStack()->push($stack);
        }

        // New push the specified pos in stacks
        $this->context->vmStack()->push($first);

        return ProcessedStatus::SUCCESS;
    }
}
