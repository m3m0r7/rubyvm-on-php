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
    private int $pointer = 0;

    private int $pointerKey = 0;

    private int $behindPos = 0;

    public function __construct(
        private readonly NumberSymbol|NilSymbol $begin,
        private readonly NumberSymbol|NilSymbol $end,
        private readonly bool $excludeEnd,
    ) {
        $this->pointer = $this->begin->valueOf() ?? 0;
        $this->behindPos = $this->begin->valueOf() ?? 0;
    }

    public function count(): int
    {
        if ($this->isInfinity()) {
            throw new SymbolUnsupportedException('The range symbol cannot count items because end of value is an infinity');
        }

        return $this->end->valueOf() ?? 0;
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
        return $this->shouldBeExists($offset - $this->behindPos);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->shouldBeExists($offset - $this->behindPos)) {
            return new NumberSymbol(
                $offset - $this->behindPos,
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
        if ($this->isInfinity() || $this->isNegativeInfinity()) {
            return true;
        }

        if (!$this->excludeEnd && $value <= $this->count()) {
            return true;
        }

        return $this->excludeEnd && $value < $this->count();
    }

    public function current(): mixed
    {
        return new NumberSymbol($this->pointer);
    }

    public function next(): void
    {
        ++$this->pointer;
        ++$this->pointerKey;
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
        $this->pointer = $this->begin->valueOf() ?? 0;
        $this->pointerKey = 0;
    }

    public function isInfinity(): bool
    {
        return $this->end instanceof NilSymbol;
    }

    public function isNegativeInfinity(): bool
    {
        return $this->begin instanceof NilSymbol;
    }
}
