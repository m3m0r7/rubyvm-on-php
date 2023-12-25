<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence;

use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\VariableInterface;

class Variable implements VariableInterface
{
    public function __construct(
        public readonly int $flipCount,
        public readonly mixed $scriptLines,
    ) {}
}
