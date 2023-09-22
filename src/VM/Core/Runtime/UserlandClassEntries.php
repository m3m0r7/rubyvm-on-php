<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;

final class UserlandClassEntries extends AbstractEntries
{
    /**
     * When $value is a string, which is set an alias on PHP method/function
     * When $value is a ContextInterface, which is having native code
     * Otherwise, set deeply entry when $value is instantiated by UserlandHeapSpaceInterface
     */
    public function verify(mixed $value): bool
    {
        return is_string($value) || $value instanceof ContextInterface || $value instanceof UserlandHeapSpaceInterface;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
