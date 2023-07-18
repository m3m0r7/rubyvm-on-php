<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class OffsetSymbol implements SymbolInterface
{
    public function __construct(
        public readonly int $offset,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->offset;
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::SYMBOL,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
