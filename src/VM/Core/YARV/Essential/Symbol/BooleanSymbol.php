<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class BooleanSymbol implements SymbolInterface, \Stringable
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
        return (string) $this->boolean !== '' && (string) $this->boolean !== '0'
            ? 'true'
            : 'false';
    }
}
