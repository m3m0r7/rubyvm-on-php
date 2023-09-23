<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ID;
use RubyVM\VM\Core\Runtime\RubyClass;

class OperandEntry
{
    public function __construct(
        public RubyClass|CallInfoEntryInterface|RubyClassInterface|ID|ExecutedResult $operand
    ) {}

    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
