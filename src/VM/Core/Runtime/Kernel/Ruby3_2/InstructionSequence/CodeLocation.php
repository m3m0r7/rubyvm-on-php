<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CodeLocationInterface;

class CodeLocation implements CodeLocationInterface
{
    public function __construct(
        public readonly CodePosition $begin,
        public readonly CodePosition $end,
    ) {}
}
