<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;

class InstructionSequences extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof InstructionSequenceInterface;
    }
}
