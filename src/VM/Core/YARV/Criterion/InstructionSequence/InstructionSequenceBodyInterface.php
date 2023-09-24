<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface InstructionSequenceBodyInterface
{
    public function info(): InstructionSequenceInfoInterface;
}
