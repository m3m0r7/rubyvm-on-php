<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;

class CallInfoEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof CallInfoEntryInterface;
    }
}
