<?php
declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\InstructionSequence;

interface InstructionSequenceProcessorInterface
{
    public function process(): InstructionSequenceBody;
}
