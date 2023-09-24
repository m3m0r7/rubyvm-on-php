<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Criterion\Entry;

interface EntriesInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @return mixed[]
     */
    public function toArray(): array;
}
