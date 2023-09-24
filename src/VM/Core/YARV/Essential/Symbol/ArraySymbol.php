<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

/**
 * @implements \ArrayAccess<int, SymbolInterface>
 * @implements \IteratorAggregate<int, SymbolInterface>
 */
class ArraySymbol implements SymbolInterface, \ArrayAccess, \Countable, \IteratorAggregate, \Stringable
{
    /**
     * @param array<int, SymbolInterface> $array
     */
    public function __construct(
        private array $array,
    ) {}

    /**
     * @return array<int, SymbolInterface>
     */
    public function valueOf(): array
    {
        return $this->array;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s]',
            implode(', ', array_map(
                static fn ($value) => (string) $value,
                $this->array,
            ))
        );
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->array);
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
        $this->array[$offset === null ? count($this->array) : $offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->array[$offset]);
    }

    public function count(): int
    {
        return count($this->array);
    }
}
