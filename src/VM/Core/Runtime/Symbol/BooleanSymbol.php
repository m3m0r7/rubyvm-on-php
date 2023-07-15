<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class BooleanSymbol implements SymbolInterface
{
    public function __construct(
        public readonly bool $boolean,
    )
    {
    }

    public function __toString(): string
    {
        return (string) $this->boolean
            ? 'true'
            : 'false';
    }
}
