<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class CaseDispatchSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly SymbolInterface $hash,
        private readonly int $pos,
        private readonly int $len,
    ) {}

    public function valueOf(): mixed
    {
        return $this->hash->valueOf();
    }

    public function __toString(): string
    {
        return '';
    }

    public function pos(): int
    {
        return $this->pos;
    }

    public function len(): int
    {
        return $this->len;
    }
}
