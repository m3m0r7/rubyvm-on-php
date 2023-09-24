<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CodePositionInterface;

class CodePosition implements CodePositionInterface
{
    public function __construct(
        public readonly int $lineNumber,
        public readonly int $column,
    ) {}
}
