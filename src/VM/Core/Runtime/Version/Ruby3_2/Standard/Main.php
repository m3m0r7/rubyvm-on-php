<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Main implements MainInterface
{
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
}
