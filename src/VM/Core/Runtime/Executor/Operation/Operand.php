<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\ID;

class Operand
{
    public function __construct(
        public CallInfoInterface|RubyClassInterface|ID|ExecutedResult|ContextInterface $operand
    ) {}

    public function __clone()
    {
        $this->operand = clone $this->operand;
    }
}
