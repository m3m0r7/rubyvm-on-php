<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\Runtime\InstructionSequence\InsnsInterface;

class Insns implements InsnsInterface
{
    public function __construct(
        public readonly int $size,
        public readonly mixed $body,
        public readonly mixed $positions,
    ) {}
}
