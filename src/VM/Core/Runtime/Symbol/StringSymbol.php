<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Encoding;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;

class StringSymbol implements SymbolInterface, RubyClassImplementationInterface
{
    use ShouldBeRubyClass;

    public function __construct(
        public readonly string $string,
        public readonly Encoding $encoding = Encoding::RUBY_ENCINDEX_UTF_8,
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::STRING,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
