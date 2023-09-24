<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\LocationInterface;

class Location implements LocationInterface
{
    public function __construct(
        public readonly int $firstLineNo,
        public readonly int $nodeId,
        public readonly CodeLocation $codeLocation,
    ) {}
}
