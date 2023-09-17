<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\IDList;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class OperandEntry
{
    public function __construct(
        public Object_|CallInfoEntryInterface|RubyClassInterface|ID|ExecutedResult|IDList $operand
    ) {
    }

    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
