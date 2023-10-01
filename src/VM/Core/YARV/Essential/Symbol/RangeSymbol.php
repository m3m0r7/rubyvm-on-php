<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Exception\SymbolUnsupportedException;

/**
 * @implements \ArrayAccess<int, NumberSymbol>
 * @implements \Iterator<int, SymbolInterface>
 */
class RangeSymbol implements SymbolInterface, \ArrayAccess, \Countable, \Stringable, \Iterator
{
    private ?int $pointer = null;
    private int $pointerKey = 0;

    private int $behindPos = 0;

    public function __construct(
        private readonly NumberSymbol|NilSymbol $begin,
        private readonly NumberSymbol|NilSymbol $end,
        private readonly bool $excludeEnd,
    ) {
        $this->pointer = $this->begin->valueOf();
        $this->behindPos = $this->begin->valueOf();
    }

    public function count(): int
    {
        if ($this->end instanceof NilSymbol) {
            return PHP_INT_MAX;
        }
        return $this->end->valueOf();
    }

    /**
     * @return array<int, NumberSymbol>
     */
    public function valueOf(): array
    {
        return [];
    }

    public function __toString(): string
    {
        $dots = ($this->excludeEnd ? '...' : '..');

        if ($this->begin instanceof NilSymbol) {
            return $dots . "{$this->end->valueOf()}";
        }

        if ($this->end instanceof NilSymbol) {
            return "{$this->begin->valueOf()}" . $dots;
        }

        return "{$this->begin->valueOf()}" . $dots . "{$this->end->valueOf()}";
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->shouldBeExists((int) $offset - $this->behindPos);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->shouldBeExists((int) $offset - $this->behindPos)) {
            return new NumberSymbol(
                (int) $offset - $this->behindPos,
            );
        }
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new SymbolUnsupportedException('The range symbol cannot appending new value');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new SymbolUnsupportedException('The range symbol cannot unset a value');
    }

    private function shouldBeExists(int $value): bool
    {
        if (!$this->end instanceof NumberSymbol) {
            return true;
        }
        if (!$this->excludeEnd && $value <= $this->count()) {
            return true;
        }
        if ($this->excludeEnd && $value < $this->count()) {
            return true;
        }
        return false;
    }

    public function current(): mixed
    {
        return new NumberSymbol($this->pointer);
    }

    public function next(): void
    {
        $this->pointer++;
        $this->pointerKey++;
    }

    public function key(): mixed
    {
        return $this->pointerKey;
    }

    public function valid(): bool
    {
        return $this->shouldBeExists($this->pointer);
    }

    public function rewind(): void
    {
        $this->pointer = $this->begin->valueOf();
        $this->pointerKey = 0;
    }
}
