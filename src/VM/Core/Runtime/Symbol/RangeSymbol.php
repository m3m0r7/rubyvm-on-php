<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Option;

class RangeSymbol implements SymbolInterface
{
    public function __construct(
        public readonly NumberSymbol $begin,
        public readonly NumberSymbol $end,
        public readonly bool $excludeEnd,
        public readonly int $steps = 1,
    ) {
    }

    public function __toString(): string
    {
        return (string) "<RangeSymbol: {$this->begin->number}..{$this->end->number}>";
    }

    public function each(OperationProcessorContext $context): SymbolInterface
    {
        $end = $this->end->number + ($this->excludeEnd ? 0 : 1);
        for ($i = $this->begin->number; $i < $end; $i += $this->steps) {
            $executor = (new Executor(
                currentDefinition: $context->executor()->currentDefinition(),
                kernel: $context->kernel(),
                main: $context->self(),
                operationProcessorEntries: $context->operationProcessorEntries(),
                instructionSequence: $context->instructionSequence(),
                logger: $context->logger(),
                debugger: $context->debugger(),
                previousContext: $context,
            ));

            $executor->context()
                ->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    (new NumberSymbol($i))->toObject()
                )
            ;

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->throwed) {
                throw $result->throwed;
            }
        }

        return new NilSymbol();
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
