<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface InstructionSequenceProcessorInterface
{
    public function process(): InstructionSequenceBodyInterface;
}
