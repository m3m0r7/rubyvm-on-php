<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Encoding;

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
        return (string) "<Unknown>";
    }
}
