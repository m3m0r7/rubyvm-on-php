<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class ClassSymbol implements SymbolInterface
{
    public function __construct(
        private StringSymbol $class,
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
