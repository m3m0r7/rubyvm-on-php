<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class NilSymbol implements SymbolInterface, \Stringable
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
}
