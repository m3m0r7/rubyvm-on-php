<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;

class FloatSymbol implements SymbolInterface, RubyClassInterface
{
    use ShouldBeRubyClass;

    public function __construct(
        public readonly float $number,
    ) {
    }

    public function __toString(): string
    {
        $hasFraction = str_contains((string) $this->number, '.');
        if ($hasFraction) {
            return (string) rtrim(
                sprintf(
                    '%.16f',
                    $this->number
                ),
                '0'
            );
        }

        return "{$this->number}.0";
    }

    public function toObject(): Object_
    {
        return new Object_(
            info: new ObjectInfo(
                type: SymbolType::FLOAT,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }
}
