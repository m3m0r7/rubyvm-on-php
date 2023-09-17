<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\LocalTableHelper;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard\Main;
use RubyVM\VM\Core\Runtime\VMCallFlagBit;
use RubyVM\VM\Exception\OperationProcessorException;

trait CallBlockHelper
{
    private function callSimpleMethod(ContextInterface $context, SymbolInterface|ContextInterface ...$arguments): ExecutedResult|null
    {
        // Validate first value is context?
        $calleeContexts = [];
        if (isset($arguments[0]) && $arguments[0] instanceof ContextInterface) {
            $calleeContexts = [array_shift($arguments)];
        }

        $executor = (new Executor(
            kernel: $context->kernel(),
            rubyClass: $context->self(),
            instructionSequence: $context->instructionSequence(),
            logger: $context->logger(),
            debugger: $context->debugger(),
            previousContext: $context
                ->renewEnvironmentTable(),
        ));

        // TODO: is this needed?
        if (!$context->self() instanceof Main) {
            $executor->context()->vmStack()->push(
                new OperandEntry(
                    $context->self(),
                ),
            );
        }

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

        return $executor
            ->execute(...$calleeContexts)
        ;
    }

    private function callBlockWithArguments(CallInfoEntryInterface $callInfo, NumberSymbol $blockIseqIndex, Object_|RubyClassInterface $blockObject, bool $isSuper, OperandEntry ...$arguments): ?Object_
    {
        if ($callInfo->callData()->flag() & (0x01 << VMCallFlagBit::VM_CALL_ARGS_BLOCKARG->value)) {
            throw new OperationProcessorException('The callBlockWithArguments is not implemented yet');
        }
        if ($blockIseqIndex->number === 0) {
            // TODO: implement a super call
            // see: https://github.com/ruby/ruby/blob/ruby_3_2/vm_args.c#L888

            throw new OperationProcessorException('The callBlockWithArguments is not implemented yet');
        }
        $instructionSequence = $this->context
            ->kernel()
            ->loadInstructionSequence(new Aux(
                loader: new AuxLoader(
                    index: $blockIseqIndex->number,
                ),
            ))
        ;

        $instructionSequence->load();

        $executor = (new Executor(
            kernel: $this->context->kernel(),
            rubyClass: $this->context->self(),
            instructionSequence: $instructionSequence,
            logger: $this->context->logger(),
            debugger: $this->context->debugger(),
            previousContext: $this->context,
        ));

        $result = null;
        $callee = null;
        $arguments = $this->translateForArguments(
            ...$arguments
        );

        if ($blockObject instanceof Object_) {
            $callee = $blockObject->symbol;
        } else {
            $callee = $blockObject;
        }

        $result = $callee->{(string) $callInfo->callData()->mid()->object->symbol}(
            $executor->context(),
            ...$arguments,
        );

        if ($result instanceof SymbolInterface) {
            return $result->toObject();
        }

        return null;
    }
}
