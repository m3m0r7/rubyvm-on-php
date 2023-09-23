<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

class RangeSymbol implements SymbolInterface, \ArrayAccess
{
    private array $array;

    public function __construct(
        private readonly NumberSymbol $begin,
        private readonly NumberSymbol $end,
        private readonly bool $excludeEnd,
        private readonly int $steps = 1,
    ) {
        if ($this->begin->valueOf() > $this->end->valueOf()) {
            $this->array = [];

            return;
        }

        $array = [];
        foreach (range(
            $this->begin->valueOf(),
            $this->end->valueOf() - ($this->excludeEnd ? 1 : 0),
            $this->steps,
        ) as $i) {
            $array[] = new NumberSymbol($i);
        }

        $this->array = $array;
    }

    public function valueOf(): array
    {
        return $this->array;
    }

    public function __toString(): string
    {
        return "{$this->begin->valueOf()}" . ($this->excludeEnd ? '...' : '..') . "{$this->end->valueOf()}";
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->array[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->array[$offset]);
    }
}
