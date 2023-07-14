<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;
use RubyVM\VM\Stream\StreamHandlerInterface;

class Option
{
    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
        public readonly ?StreamHandlerInterface $stdOut = null,
        public readonly ?StreamHandlerInterface $stdIn = null,
        public readonly ?StreamHandlerInterface $stdErr = null,
    ) {
    }
}
