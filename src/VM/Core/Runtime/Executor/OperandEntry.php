<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Object_;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ID;

class OperandEntry
{
    public function __construct(
        public Object_|CallInfoEntryInterface|RubyClassInterface|ID|ExecutedResult $operand
    ) {}

    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
