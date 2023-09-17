<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class UndefinedSymbol implements SymbolInterface
{
    public function __construct() {}

    public function __toString(): string
    {
        return '<undefined>';
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::UNDEF,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
