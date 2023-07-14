<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Encoding;

class StringSymbol implements SymbolInterface
{
    public function __construct(
        public readonly string $string,
        public readonly Encoding $encoding,
    ) {}

    public function __toString(): string
    {
        return $this->string;
    }
}
