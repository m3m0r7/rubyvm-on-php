<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class ClassSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly StringSymbol|SymbolSymbol $class,
    ) {}

    public function valueOf(): string
    {
        return $this->class->valueOf();
    }

    public function __toString(): string
    {
        return $this->valueOf();
    }
}
