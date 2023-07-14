<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;

class Option
{
    public function __construct(
        public readonly BinaryStreamReaderInterface $reader,
        public readonly LoggerInterface $logger,
    ) {}
}
