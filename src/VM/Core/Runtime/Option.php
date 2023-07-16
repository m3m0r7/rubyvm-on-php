<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Option
{
    const DETECT_INFINITY_LOOP = 3;
    const MAX_STACK_EXCEEDED = 255;
    const RUBY_ENCINDEX_BUILTIN_MAX = 12;
    const VM_ENV_DATA_SIZE = 3;

    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
        public readonly ?StreamHandlerInterface $stdOut = null,
        public readonly ?StreamHandlerInterface $stdIn = null,
        public readonly ?StreamHandlerInterface $stdErr = null,
    ) {
    }
}
