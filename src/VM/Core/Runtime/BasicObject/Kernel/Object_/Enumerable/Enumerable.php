<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Object_;

abstract class Enumerable extends Object_ implements \ArrayAccess {

    public function offsetExists(mixed $offset): bool
    {
        return $this->symbol->offsetExists($offset);
    }


    public function offsetGet(mixed $offset): mixed
    {
        return $this->symbol->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->symbol->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->symbol->offsetUnset($offset);
    }
}
