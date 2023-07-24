<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;

class DefinedClassEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return class_exists($value);
    }

    public function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
