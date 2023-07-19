<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Executor;
use RubyVM\VM\Core\Runtime\Executor\OperationProcessorContext;
use RubyVM\VM\Core\Runtime\Option;

class ArraySymbol implements SymbolInterface
{
    public function __construct(
        public readonly array $array,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '<array: %d>',
            count($this->array)
        );
    }

    public function each(ContextInterface $context): void
    {
        for ($i = 0; $i < count($this->array); $i++) {

            $executor = (new Executor(
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
                    (new NumberSymbol($this->array[$i]->number))
                        ->toObject()
                );

            $result = $executor->execute();

            // An occurred exception to be throwing
            if ($result->throwed) {
                throw $result->throwed;
            }
        }
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::ARRAY,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
