<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

interface InstructionSequenceBodyInterface
{
    public function type(): int;
    public function stackMax(): int;
}
