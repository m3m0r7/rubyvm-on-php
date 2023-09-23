<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\VariableInterface;

class Variable implements VariableInterface
{
    public function __construct(
        public readonly int $flipCount,
        public readonly mixed $scriptLines,
    ) {}
}
