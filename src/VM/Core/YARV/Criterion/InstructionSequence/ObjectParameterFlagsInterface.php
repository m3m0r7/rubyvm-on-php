<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface ObjectParameterFlagsInterface
{
    public function hasRest(): bool;
}
