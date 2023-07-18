<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Exception\RubyVMException;

class StructSymbol implements SymbolInterface
{
    public function __construct(
        public readonly int $classIndex,
        public readonly int $len,
        public readonly int $begin,
        public readonly int $end,
        public readonly int $excl,
        public readonly SymbolInterface $symbol,
    ) {
    }

    public function __toString(): string
    {
        return (string) '<Unknown>';
    }

    public function each(OperationProcessorContext $context): SymbolInterface
    {
        $rangeSymbol = $this->symbol;
        if (!($rangeSymbol instanceof RangeSymbol)) {
            throw new RubyVMException(
                sprintf(
                    'The bound symbol is expected a RangeSymbol but actually `%s`',
                    get_class($rangeSymbol)
                )
            );
        }

        $end = $rangeSymbol->end->number + ($rangeSymbol->excludeEnd ? 0 : 1);
        for ($i = $rangeSymbol->begin->number; $i < $end; $i += $rangeSymbol->steps) {
            $context->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    (new NumberSymbol($i))->toObject()
                );

            $result = (new Executor(
                kernel: $context->kernel(),
                main: $context->self(),
                operationProcessorEntries: $context->operationProcessorEntries(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                environmentTableEntries: $context->environmentTableEntries(),
                debugger: $context->debugger(),
                previousContext: $context,
            ))->enableBreakpoint($context->executor()->breakPoint())->execute();

            // An occurred exception to be throwing
            if ($result->throwed) {
                throw $result->throwed;
            }
        }

        return (new NilSymbol());
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::STRUCT,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
