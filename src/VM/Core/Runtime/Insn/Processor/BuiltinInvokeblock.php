<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Insn\Processor;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperandEntry;
use RubyVM\VM\Core\Runtime\Executor\OperandHelper;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorInterface;
use RubyVM\VM\Core\Runtime\Executor\ProcessedStatus;
use RubyVM\VM\Core\Runtime\Executor\Translatable;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

class BuiltinInvokeblock implements OperationProcessorInterface
{
    use Translatable;
    use OperandHelper;
    private Insn $insn;

    private ContextInterface $context;

    public function prepare(Insn $insn, ContextInterface $context): void
    {
        $this->insn = $insn;
        $this->context = $context;
    }

    public function before(): void
    {
    }

    public function after(): void
    {
    }

    public function process(mixed ...$arguments): ProcessedStatus
    {
        if (!isset($arguments[0]) || !$arguments[0] instanceof OperationProcessorContext) {
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
        $processor = $arguments[0];

        $operand = $this->getOperandAsCallInfo();
        $arguments = [];

        for ($i = 0; $i < $operand->callData()->argumentsCount(); ++$i) {
            $arguments[] = $this->getStackAsSymbol();
        }

        $executor = (new Executor(
            kernel: $processor->kernel(),
            rubyClass: $processor->self(),
            instructionSequence: $processor->instructionSequence(),
            logger: $processor->logger(),
            debugger: $processor->debugger(),
            previousContext: $processor
                ->renewEnvironmentTable(),
        ));

        $localTableSize = $executor->context()->instructionSequence()->body()->data->localTableSize();

        for ($localIndex = 0, $i = count($arguments) - 1; $i >= 0; $i--, $localIndex++) {
            /**
             * @var SymbolInterface $argument
             */
            $argument = $arguments[$i];
            $executor->context()
                ->environmentTable()
                ->set(
                    LocalTableHelper::computeLocalTableIndex(
                        $localTableSize,
                        Option::VM_ENV_DATA_SIZE + $localTableSize - $localIndex - 1,
                    ),
                    $argument->toObject(),
                )
            ;
        }

        $result = $executor->context()->executor()->execute();

        if (null === $result) {
            // This is same at UNDEFINED on originally RubyVM
            return ProcessedStatus::SUCCESS;
        }

        if ($result instanceof ExecutedResult) {
            if ($result->threw) {
                throw $result->threw;
            }
            if (null !== $result->returnValue) {
                $this->context->vmStack()
                    ->push(new OperandEntry($result->returnValue))
                    // TODO: is this correctly?
                    ->dup()
                ;
            }

            return ProcessedStatus::SUCCESS;
        }

        if ($result instanceof SymbolInterface) {
            $this->context->vmStack()
                ->push(new OperandEntry($result->toObject()))
                // TODO: is this correctly?
                ->dup()
            ;

            return ProcessedStatus::SUCCESS;
        }

        throw new OperationProcessorException('Unreachable here because the invokeblock is not properly implementation');
    }
}
