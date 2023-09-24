<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

readonly class InstructionSequenceBody implements InstructionSequenceBodyInterface
{
    public function __construct(
        private InstructionSequenceInfoInterface $info,
    ) {}

    public function info(): InstructionSequenceInfoInterface
    {
        return $this->info;
    }
}
