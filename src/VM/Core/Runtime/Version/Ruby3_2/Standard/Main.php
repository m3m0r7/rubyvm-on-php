<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Main implements MainInterface
{
    public function __construct(
        private readonly StreamHandlerInterface $stdout,
        private readonly StreamHandlerInterface $stdin,
        private readonly StreamHandlerInterface $stderr,
    ) {
    }

    public function puts(StringSymbol $stringSymbol): void
    {
        $this->stdout->write($stringSymbol->string);
    }
}
