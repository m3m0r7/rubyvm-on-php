<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Criterion\Entry;

use RubyVM\VM\Exception\EntryException;

abstract class AbstractEntries implements EntriesInterface
{
    public function __construct(public array $items = []) {}

    public function __clone(): void
    {
        foreach ($this->items as $key => $value) {
            $this->items[$key] = is_object($value)
                ? clone $value
                : $value;
        }
    }

    public function verify(mixed $value): bool
    {
        return true;
    }

    public function verifyOffset(mixed $key): bool
    {
        if (EntryType::HASH === $this->entryType()) {
            if ($key === null) {
                return false;
            }

            return is_string($key) || is_int($key);
        }

        return null === $key || is_int($key);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($this->filterKeyName($offset), $this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$this->filterKeyName($offset)] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$this->verify($value)) {
            throw new EntryException(sprintf('The entry value is not verified on %s (%s)', static::class, get_debug_type($value)));
        }

        if (!$this->verifyOffset($offset)) {
            throw new EntryException(sprintf('The entry key is not verified on %s (%s)', static::class, get_debug_type($value)));
        }

        $this->items[$offset ? $this->filterKeyName($offset) : count($this->items)] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function append(mixed ...$values): void
    {
        foreach ($values as $value) {
            $this->offsetSet(null, $value);
        }
    }

    public function set(mixed $index, mixed $value): self
    {
        $this->offsetSet($index, $value);

        return $this;
    }

    public function has(mixed $index): bool
    {
        return $this->offsetExists($index);
    }

    public function get(mixed $index): mixed
    {
        return $this->offsetGet($index) ?? null;
    }

    protected function entryType(): EntryType
    {
        return EntryType::LIST;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    protected function filterKeyName(mixed $index): string|int|null
    {
        return $this->enumToName($index);
    }

    private function enumToName(mixed $index): string|int
    {
        if (is_object($index) && enum_exists($index::class, false)) {
            return $index->name;
        }

        return $index;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
