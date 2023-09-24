<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\YARV\Essential\Encoding;

class StringSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly string $string,
        // @phpstan-ignore-next-line
        private readonly Encoding $encoding = Encoding::RUBY_ENCINDEX_UTF_8,
    ) {}

    public function valueOf(): string
    {
        return $this->string;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
