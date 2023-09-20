<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

interface RubyVMBinaryStreamReaderInterface extends BinaryStreamReaderInterface
{
    /**
     * @return RubyVMBinaryStreamReader
     */
    public function duplication(): self;

    public function smallValue(): int;
}
