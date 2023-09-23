<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\Operation\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\Operation\Processor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Translatable;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinInvokeblock implements OperationProcessorInterface
{
    use Translatable;
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

    public function process(ContextInterface|RubyClassInterface ...$arguments): ProcessedStatus
    {
        if (!isset($arguments[0]) || !$arguments[0] instanceof ContextInterface) {
            throw new OperationProcessorException(
                sprintf(
                    'The invokeblock did not get an operation processor context (actual: %s)',
                    isset($arguments[0])
                        ? ClassHelper::nameBy($arguments[0])
                        : 'null',
                )
            );
        }

        // This is an operation processor context including instruction sequence context
        $processorContext = $arguments[0];

        $operand = $this->getOperandAsCallInfo();
        $arguments = [];

        for ($i = 0; $i < $operand->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->getStackAsObject();
        }

        $executed = $this
            ->callSimpleMethod(
                $processorContext,
                ...$arguments,
            );

        if ($executed->threw) {
            throw $executed->threw;
        }
        if ($executed->returnValue !== null) {
            $this->context->vmStack()
                ->push(new OperandEntry($executed->returnValue));
        }

        if ($executed->returnValue === null) {
            return ProcessedStatus::SUCCESS;
        }

        if ($executed !== null) {
            // TODO: is this correctly?
            $this->context->vmStack()->dup();
        }

        return ProcessedStatus::SUCCESS;
    }
}
