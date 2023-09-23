<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoEntryInterface;

class CallInfoEntry implements CallInfoEntryInterface
{
    public function __construct(
        public readonly ?CallDataInterface $callData = null,
    ) {}

    public function callData(): ?CallDataInterface
    {
        return $this->callData;
    }
}
