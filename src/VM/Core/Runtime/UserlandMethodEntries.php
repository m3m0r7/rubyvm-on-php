<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;

final class UserlandMethodEntries extends AbstractEntries
{
    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
