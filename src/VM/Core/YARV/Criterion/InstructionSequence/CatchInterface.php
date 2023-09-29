<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

interface CatchInterface
{
    public function start(): int;

    public function end(): int;

    public function instructionSequence(): InstructionSequenceInterface;

    public function cont(): int;
}
