<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class VoidSymbol implements SymbolInterface
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return '<void>';
    }
}
