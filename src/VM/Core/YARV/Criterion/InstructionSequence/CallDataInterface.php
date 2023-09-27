<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\YARV\Essential\ID;

interface CallDataInterface
{
    public function flag(): int;

    public function mid(): ID;

    public function argumentsCount(): int;

    public function keywords(): ?array;
}
