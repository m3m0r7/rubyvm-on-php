<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Criterion\Entry\EntryType;

class AliasEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return is_string($value);
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
