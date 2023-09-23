<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Encoding;

class StringSymbol implements SymbolInterface
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

    public function toRubyClass(): RubyClass
    {
        return new RubyClass(
            info: new ObjectInfo(
                type: SymbolType::STRING,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }

    public function testValue(): bool
    {
        return (bool) $this->valueOf();
    }
}
