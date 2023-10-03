<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

class SimpleCallInfo implements CallInfoInterface
{
    public function __construct(private readonly RubyClassInterface $rubyClass, private readonly string $name) {}

    public function callData(): CallDataInterface
    {
        return new SimpleCallData(
            $this->rubyClass,
            $this->name,
        );
    }
}
