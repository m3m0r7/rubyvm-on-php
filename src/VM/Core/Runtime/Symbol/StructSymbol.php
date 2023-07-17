<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

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

    public function each(OperationProcessorContext $context): void
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
                    new Object_(
                        info: new ObjectInfo(
                            type: SymbolType::FIXNUM,
                            specialConst: 1,
                            frozen: 1,
                            internal: 1,
                        ),
                        symbol: new NumberSymbol(
                            $i,
                        ),
                    )
                );
            $context->executor()->execute();
        }
    }
}
