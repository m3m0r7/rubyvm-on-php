<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\Runtime\ID;

interface CallDataInterface
{
    public function flag(): int;

    public function mid(): ID;

    public function argumentsCount(): int;
}
