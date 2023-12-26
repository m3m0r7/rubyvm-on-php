<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\InsnsInterface;

class Insns implements InsnsInterface
{
    public function __construct(
        public readonly int $size,
        public readonly mixed $body,
        public readonly mixed $positions,
    ) {}
}
