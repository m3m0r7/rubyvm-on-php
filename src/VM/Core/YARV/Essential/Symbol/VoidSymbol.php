<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Object_;

class VoidSymbol implements SymbolInterface
{
    public function __construct() {}

    public function __toString(): string
    {
        return 'void';
    }

    public function valueOf(): null
    {
        return null;
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
