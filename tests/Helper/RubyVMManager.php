<?php

declare(strict_types=1);

namespace Tests\RubyVM\Helper;

use RubyVM\VM\Core\Runtime\RubyVM;
use RubyVM\VM\Stream\StreamHandlerInterface;

readonly class RubyVMManager
{
    public function __construct(
        public RubyVM $rubyVM,
        public StreamHandlerInterface $stdOut,
        public StreamHandlerInterface $stdIn,
        public StreamHandlerInterface $stdErr,
    ) {}
}
