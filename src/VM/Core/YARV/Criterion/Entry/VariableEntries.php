<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;

class VariableEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof VariableEntry;
    }
}
