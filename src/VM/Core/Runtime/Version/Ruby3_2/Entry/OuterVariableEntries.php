<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;

class OuterVariableEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof OuterVariableEntry;
    }
}
