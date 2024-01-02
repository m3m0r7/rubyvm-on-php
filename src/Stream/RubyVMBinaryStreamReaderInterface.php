<?php

declare(strict_types=1);

namespace RubyVM\Stream;

interface RubyVMBinaryStreamReaderInterface extends BinaryStreamReaderInterface
{
    /**
     * @return RubyVMBinaryStreamReader
     */
    public function duplication(): self;

    public function smallValue(): int;
}
