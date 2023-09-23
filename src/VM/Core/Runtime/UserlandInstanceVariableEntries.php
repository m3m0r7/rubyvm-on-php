<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\YARV\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\EntryType;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;

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
