<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Accessor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class BridgeCallData implements CallDataInterface
{
    /**
     * @param RubyClassInterface[] $arguments
     */
    public function __construct(private readonly string $name, private readonly array $arguments) {}

    public function flag(): int
    {
        // Always public
        return 0;
    }

    public function mid(): ID
    {
        return new ID(new StringSymbol($this->name));
    }

    public function argumentsCount(): int
    {
        return count($this->arguments);
    }

    public function keywords(): ?array
    {
        // TODO: we must implement accepting keywords arguments on PHP
        return null;
    }
}
