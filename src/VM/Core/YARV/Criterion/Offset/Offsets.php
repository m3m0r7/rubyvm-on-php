<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Offset;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;

class Offsets extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof Offset;
    }
}
