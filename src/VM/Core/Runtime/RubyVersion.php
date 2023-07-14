<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use Psr\Log\LoggerInterface;
use RubyVM\VM\Stream\BinaryStreamReaderInterface;

enum RubyVersion: string
{
    case VERSION_3_2 = '3.2';
    case VERSION_3_3 = '3.3';
}
