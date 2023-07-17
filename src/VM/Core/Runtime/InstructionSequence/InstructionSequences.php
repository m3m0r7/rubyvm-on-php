<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;

class InstructionSequences extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof InstructionSequenceInterface;
    }
}
