<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class RangeSymbol implements SymbolInterface
{
    public function __construct(
        public readonly NumberSymbol $begin,
        public readonly NumberSymbol $end,
        public readonly bool $excludeEnd,
        public readonly int $steps = 1,
    ) {
    }

    public function __toString(): string
    {
        return (string) "<RangeSymbol: {$this->begin->number}..{$this->end->number}>";
    }

    public function toObject(): Object_
    {
        return $this->toObject();
    }
}
