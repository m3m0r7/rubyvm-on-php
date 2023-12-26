<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class RegexpSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly StringSymbol $source,
        private readonly null|int $option,
    ) {}

    public function valueOf(): StringSymbol
    {
        return $this->source;
    }

    public function __toString(): string
    {
        return (string) $this->source;
    }

    public function option(): null|int
    {
        return $this->option;
    }
}
