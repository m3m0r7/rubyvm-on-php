<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\EntryType;

final class UserlandMethodEntries extends AbstractEntries
{
    /**
     * When $value is a string, which is set an alias on PHP method/function
     * When $value is a ContextInterface, which is having native code.
     */
    public function verify(mixed $value): bool
    {
        return is_string($value) || $value instanceof ContextInterface;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
