<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

interface EntriesInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    public function toArray(): array;
}
