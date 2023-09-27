<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

class BridgeCallInfo implements CallInfoInterface
{
    /**
     * @param RubyClassInterface[] $arguments
     */
    public function __construct(private readonly string $name, private readonly array $arguments) {}

    public function callData(): CallDataInterface
    {
        return new BridgeCallData(
            $this->name,
            $this->arguments,
        );
    }
}
