<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class BooleanSymbol implements SymbolInterface
{
    public function __construct(
        public readonly bool $boolean,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->boolean
            ? 'true'
            : 'false';
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: $this->boolean
                    ? SymbolType::TRUE
                    : SymbolType::FALSE,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
