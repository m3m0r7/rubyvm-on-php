<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Runtime\Object_;

class BooleanSymbol implements SymbolInterface
{
    public function __construct(
        private readonly bool $boolean,
    ) {}

    public function valueOf(): bool
    {
        return $this->boolean;
    }

    public function __toString(): string
    {
        return (string) $this->boolean
            ? 'true'
            : 'false';
    }

    public function toString(): SymbolInterface
    {
        return new StringSymbol(
            string: (string) $this,
        );
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
