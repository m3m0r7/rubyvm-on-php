<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Offset;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Exception\VerificationException;

class Offset
{
    public function __construct(public readonly int $offset)
    {
        if ($this->offset < 0) {
            throw new VerificationException(
                sprintf(
                    'An offset cannot negative value (actual: %d)',
                    $this->offset,
                ),
            );
        }
    }

    public function increase(): Offset
    {
        return new Offset($this->offset + 1);
    }
}
