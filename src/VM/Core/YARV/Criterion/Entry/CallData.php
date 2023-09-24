<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Entry;

use RubyVM\VM\Core\Runtime\ID;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class CallData implements CallDataInterface
{
    public function __construct(
        public readonly ID $mid,
        public readonly int $flag,
        public readonly int $argc,

        /**
         * @var StringSymbol[]|null
         */
        public readonly ?array $keywords,
    ) {}

    public function flag(): int
    {
        return $this->flag;
    }

    public function mid(): ID
    {
        return $this->mid;
    }

    public function argumentsCount(): int
    {
        return $this->argc;
    }
}
