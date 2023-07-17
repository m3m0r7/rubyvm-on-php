<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

class RangeSymbol implements SymbolInterface
{
    protected readonly array $values;

    public function __construct(
        public readonly NumberSymbol $begin,
        public readonly NumberSymbol $end,
        public readonly bool $excludeEnd,
        public readonly int $steps = 1,
    ) {
        $this->values = $this->createRange();
    }

    private function createRange(): array
    {
        $values = [];

        $end = $this->end->number + (
            $this->excludeEnd
            ? 0
            : 1
        );
        for ($i = $this->begin->number; ($i < $end); $i += $this->steps) {
            $values[] = new NumberSymbol(
                $i,
                true,
            );
        }
        return $values;
    }

    public function __toString(): string
    {
        return (string) "<RangeSymbol: {$this->begin->number}..{$this->end->number}>";
    }
}
