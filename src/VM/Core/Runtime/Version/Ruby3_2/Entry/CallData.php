<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry;

use RubyVM\VM\Core\Runtime\Executor\CallDataInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;

class CallData implements CallDataInterface
{
    public function __construct(
        public readonly ID $mid,
        public readonly int $flag,
        public readonly int $argc,
        public readonly ?array $keywords,
    ) {
    }

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
