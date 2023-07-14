<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Offset;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;

class Offsets extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof Offset;
    }
}
