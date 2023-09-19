<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

interface BinaryStreamReaderHelperInterface
{
    public function pretense(callable $callback): mixed;

    public function smallValue(): int;
}
