<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Context;

use RubyVM\Stream\StreamHandlerInterface;

class IOContext
{
    public function __construct(
        public readonly StreamHandlerInterface $stdOut,
        public readonly StreamHandlerInterface $stdIn,
        public readonly StreamHandlerInterface $stdErr,
    ) {}
}
