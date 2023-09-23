<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\Object_;
use RubyVM\VM\Core\YARV\Criterion\Entry\AbstractEntries;

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
