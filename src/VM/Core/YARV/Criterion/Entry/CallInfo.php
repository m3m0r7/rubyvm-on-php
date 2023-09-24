<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

class CallInfo implements CallInfoInterface
{
    public function __construct(
        public readonly CallDataInterface $callData,
    ) {}

    public function callData(): CallDataInterface
    {
        return $this->callData;
    }
}
