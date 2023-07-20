<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

class InstructionSequenceCompileData
{
    public function __construct(
        public int $ciIndex = 0,
        public int $icSize = 0,
    )
    {
    }
}
