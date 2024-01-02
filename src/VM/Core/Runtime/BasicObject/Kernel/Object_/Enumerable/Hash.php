<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;

class Hash extends Enumerable implements RubyClassInterface
{
    /**
     * @param array<RubyClassInterface> $hash
     */
    public function __construct(protected array $hash = []) {}

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->hash);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->hash[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->hash[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->hash[$offset]);
    }

    public static function createBy(mixed $value = null): self
    {
        return new self($value);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->hash);
    }

    public function valueOf(): mixed
    {
        return $this->hash;
    }

    public function count(): int
    {
        return count($this->hash);
    }

    public function inspect(): RubyClassInterface
    {
        $keys = array_keys($this->hash);
        $values = array_values($this->hash);

        uksort($keys, static fn (int $a, $b) => $b <=> $a);
        uksort($values, static fn (int $a, $b) => $b <=> $a);

        return String_::createBy(sprintf(
            '{%s}',
            implode(
                ', ',
                array_map(
                    static fn (string $key, RubyClassInterface $value) => sprintf(
                        ':%s=>%s',
                        $key,
                        $value->inspect(),
                    ),
                    $keys,
                    $values,
                )
            ),
        ));
    }
}
