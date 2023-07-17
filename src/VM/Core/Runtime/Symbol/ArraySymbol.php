<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

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

    public function each(OperationProcessorContext $context): void
    {
        for ($i = 0; $i < count($this->array); $i++) {
            $context->environmentTableEntries()
                ->get(Option::RSV_TABLE_INDEX_0)
                ->set(
                    Option::VM_ENV_DATA_SIZE,
                    new Object_(
                        info: new ObjectInfo(
                            type: SymbolType::FIXNUM,
                            specialConst: 1,
                            frozen: 1,
                            internal: 1,
                        ),
                        symbol: new NumberSymbol(
                            $this->array[$i]->number,
                        ),
                    )
                );
            $context->executor()->execute();
        }
    }
}
