<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;
use RubyVM\VM\Core\YARV\Criterion\Essential\RubyClassInterface;

final class UserlandInstanceVariableEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof RubyClassInterface;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
