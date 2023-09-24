<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class VoidSymbol implements SymbolInterface, \Stringable
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
}
