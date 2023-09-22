<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class NilSymbol implements SymbolInterface
{
    public function __construct() {}

    public function __toString(): string
    {
        return 'nil';
    }

    public function valueOf(): null
    {
        return null;
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::NIL,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
