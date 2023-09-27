<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\YARV\Essential\Encoding;

class SymbolSymbol implements SymbolInterface, \Stringable
{
    public function __construct(
        private readonly string $string,
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

    public function encoding(): Encoding
    {
        return $this->encoding;
    }
}
