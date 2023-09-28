<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

class Hash extends Enumerable implements RubyClassInterface, \ArrayAccess
{
    /**
     * @param array<SymbolInterface> $hash
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
}