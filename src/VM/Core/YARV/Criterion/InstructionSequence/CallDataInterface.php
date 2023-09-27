<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\InstructionSequence;

use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

interface CallDataInterface
{
    public function flag(): int;

    public function mid(): ID;

    public function argumentsCount(): int;

    /**
     * @return null|StringSymbol[]
     */
    public function keywords(): ?array;
}
