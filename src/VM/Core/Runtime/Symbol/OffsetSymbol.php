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
}
