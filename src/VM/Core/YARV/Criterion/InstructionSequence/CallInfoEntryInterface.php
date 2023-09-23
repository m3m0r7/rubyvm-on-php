<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface CallInfoEntryInterface
{
    public function callData(): ?CallDataInterface;
}
