<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallDataInterface;
use RubyVM\VM\Core\YARV\Essential\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class SimpleCallData implements CallDataInterface
{
    protected readonly int $argumentCount;

    public function __construct(private readonly RubyClassInterface $rubyClass, private readonly string $name)
    {
        $reflection = new \ReflectionClass($this->rubyClass);
        $this->argumentCount = count($reflection->getMethod($name)->getParameters()) - 1;
    }

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
        return $this->argumentCount;
    }

    public function keywords(): ?array
    {
        // TODO: we must implement accepting keywords arguments on PHP
        return null;
    }
}
