<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\Aux;
use RubyVM\VM\Core\Runtime\InstructionSequence\Aux\AuxLoader;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\VMCallFlagBit;
use RubyVM\VM\Exception\OperationProcessorException;

trait CallBlockHelper
{
    private function callBlockWithArguments(CallInfoEntryInterface $callInfo, NumberSymbol $blockIseqIndex, Object_ $blockObject, bool $isSuper, OperandEntry ...$arguments): ?Object_
    {
        if ($callInfo->callData()->flag() & (0x01 << VMCallFlagBit::VM_CALL_ARGS_BLOCKARG->value)) {
            throw new OperationProcessorException(
                'The callBlockWithArguments is not implemented yet'
            );
        } elseif ($blockIseqIndex->number > 0) {
            $instructionSequence = $this->context
                ->kernel()
                ->loadInstructionSequence(new Aux(
                    loader: new AuxLoader(
                        index: $blockIseqIndex->number,
                    ),
                ));

            $instructionSequence->load();

            $executor = (new Executor(
                kernel: $this->context->kernel(),
                main: $this->context->self(),
                operationProcessorEntries: $this->context->operationProcessorEntries(),
                instructionSequence: $instructionSequence,
                logger: $this->context->logger(),
                debugger: $this->context->debugger(),
                previousContext: $this->context,
            ))->enableBreakpoint($this->context->executor()->breakPoint());

            $result = $blockObject->symbol->{(string) $callInfo->callData()->mid()->object->symbol}(
                $executor->context(),
                ...$this->translateForArguments(
                    ...$arguments
                ),
            );
            if ($result instanceof SymbolInterface) {
                return $result->toObject();
            }
            return null;
        }

        // TODO: implement a super call
        // see: https://github.com/ruby/ruby/blob/ruby_3_2/vm_args.c#L888

        throw new OperationProcessorException(
            'The callBlockWithArguments is not implemented yet'
        );
    }
}
