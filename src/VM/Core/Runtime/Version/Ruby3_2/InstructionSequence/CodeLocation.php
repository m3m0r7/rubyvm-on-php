<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\InstructionSequence\CodeLocationInterface;

class CodeLocation implements CodeLocationInterface
{
    public function __construct(
        public readonly CodePosition $begin,
        public readonly CodePosition $end,
    ) {}
}
