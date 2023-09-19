<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Stream\BinaryStreamReaderHelperInterface;

class RubyVMBinaryStreamReader extends BinaryStreamReader implements BinaryStreamReaderHelperInterface
{
    public function __construct(readonly BinaryStreamReaderInterface $reader)
    {
        parent::__construct(
            $reader->streamHandler(),
            $reader->endian(),
        );
    }

    public function pretense(callable $callback): mixed
    {
        $currentPos = $this->pos();

        try {
            return $callback($this);
        } finally {
            $this->pos($currentPos);
        }
    }

    /**
     * @see https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11299
     */
    public function smallValue(): int
    {
        $offset = $this->pos();

        // Emulates: rb_popcount32(uint32_t x)
        $ntzInt32 = function (int $x): int {
            $x = ~$x & ($x - 1);
            $x = ($x & 0x55555555) + ($x >> 1 & 0x55555555);
            $x = ($x & 0x33333333) + ($x >> 2 & 0x33333333);
            $x = ($x & 0x0F0F0F0F) + ($x >> 4 & 0x0F0F0F0F);
            $x = ($x & 0x001F001F) + ($x >> 8 & 0x001F001F);
            $x = ($x & 0x0000003F) + ($x >> 16 & 0x0000003F);

            return $x;
        };

        $c = $this->readAsUnsignedByte();

        $n = ($c & 1)
            ? 1
            : (0 == $c ? 9 : $ntzInt32($c) + 1);

        $x = $c >> $n;

        if (0x7F === $x) {
            $x = 1;
        }
        for ($i = 1; $i < $n; ++$i) {
            $x <<= 8;
            $x |= $this->readAsUnsignedByte();
        }

        $this->pos(
            $offset + $n,
        );

        return $x;
    }
}
