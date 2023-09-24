<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface InstructionSequenceInterface
{
    public function body(): InstructionSequenceBodyInterface;

    public function load(): void;

    public function index(): int;
}
