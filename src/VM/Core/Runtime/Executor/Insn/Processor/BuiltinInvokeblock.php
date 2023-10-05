<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Runtime\Executor\ArgumentTransformable;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\LocalTable;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Option;

class BuiltinInvokeblock implements OperationProcessorInterface
{
    use LocalTable;
    use ArgumentTransformable;
    use OperandHelper;
    use CallBlockHelper;

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
        // This is an operation processor context including instruction sequence context
        $processorContext = $this
            ->getLocalTableToStack(
                Option::VM_ENV_DATA_SIZE - 1,
                0,
            );

        assert($processorContext instanceof ContextInterface);

        $operand = $this->operandAsCallInfo();
        $arguments = [];

        for ($i = 0; $i < $operand->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->stackAsObject();
        }

        $executed = $this
            ->callSimpleMethod(
                $processorContext,
                $operand,
                ...$arguments,
            );

        if ($executed->threw instanceof \Throwable) {
            throw $executed->threw;
        }

        if ($executed->returnValue instanceof \RubyVM\VM\Core\Runtime\Essential\RubyClassInterface) {
            $this->context->vmStack()
                ->push(new Operand($executed->returnValue));
        }

        if (!$executed->returnValue instanceof \RubyVM\VM\Core\Runtime\Essential\RubyClassInterface) {
            return ProcessedStatus::SUCCESS;
        }

        if ($executed instanceof \RubyVM\VM\Core\Runtime\Executor\ExecutedResult) {
            // TODO: is this correctly?
            $this->context->vmStack()->dup();
        }

        return ProcessedStatus::SUCCESS;
    }
}
