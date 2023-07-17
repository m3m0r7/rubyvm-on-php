<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class IDTable extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof Object_;
    }

    public function verifyOffset(mixed $key): bool
    {
        return is_int($key);
    }
}
