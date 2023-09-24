<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class UndefinedSymbol implements SymbolInterface, \Stringable
{
    public function __construct() {}

    public function __toString(): string
    {
        return 'undefined';
    }

    public function valueOf(): null
    {
        return null;
    }
}
