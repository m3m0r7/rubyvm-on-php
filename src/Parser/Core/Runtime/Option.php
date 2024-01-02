<?php

declare(strict_types=1);

namespace RubyVM\Parser\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\Stream\BinaryStreamReaderInterface;

class Option implements OptionInterface
{
    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
    ) {}
}
