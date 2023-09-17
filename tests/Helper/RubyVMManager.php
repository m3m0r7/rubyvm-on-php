<?php

declare(strict_types=1);

namespace Tests\RubyVM\Helper;

use RubyVM\VM\Core\Runtime\RubyVM;
use RubyVM\VM\Stream\StreamHandlerInterface;

class RubyVMManager
{
    public function __construct(
        public readonly RubyVM $rubyVM,
        public readonly StreamHandlerInterface $stdOut,
        public readonly StreamHandlerInterface $stdIn,
        public readonly StreamHandlerInterface $stdErr,
    ) {}
}
