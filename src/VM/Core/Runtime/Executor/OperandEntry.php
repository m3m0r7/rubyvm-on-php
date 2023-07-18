<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class OperandEntry
{
    public function __construct(
        public Object_|CallInfoEntryInterface|MainInterface|ID|ExecutedResult $operand
    ) {
    }

    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
