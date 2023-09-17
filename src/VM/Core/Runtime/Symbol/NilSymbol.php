<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;

class NilSymbol implements SymbolInterface, RubyClassInterface
{
    use ShouldBeRubyClass;

    public function __construct()
    {
    }

    public function __toString(): string
    {
        return '<nil>';
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
