<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Entry\EntryType;
use RubyVM\VM\Core\Runtime\Insn\Insn;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Exception\OperationProcessorException;

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
