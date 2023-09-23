<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

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
}
