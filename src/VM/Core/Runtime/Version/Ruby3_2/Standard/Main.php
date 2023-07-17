<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\InstructionSequence\InstructionSequence;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Main implements MainInterface
{
    protected array $userLandMethods = [];

    public function __construct(
        private readonly StreamHandlerInterface $stdOut,
        private readonly StreamHandlerInterface $stdIn,
        private readonly StreamHandlerInterface $stdErr,
    ) {
    }

    public function puts(SymbolInterface $symbol): void
    {
        $this->stdOut->write((string) $symbol);
    }

    public function phpinfo(): void
    {
        $this->stdOut->write("PHP Version: " . PHP_VERSION . "\n");
    }

    public function def(StringSymbol $methodName, InstructionSequence $instructionSequence): void
    {
        $this->userLandMethods[(string) $methodName] = $instructionSequence;
    }
}
