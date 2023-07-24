<?php


declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;

class ExtendedClassEntries extends AbstractEntries
{
    public function get(mixed $index): ?MethodExtender
    {
        return parent::get($index);
    }

    public function verify(mixed $value): bool
    {
        return $value instanceof MethodExtender;
    }

    public function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
