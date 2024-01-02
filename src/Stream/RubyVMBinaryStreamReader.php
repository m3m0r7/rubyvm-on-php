<?php

declare(strict_types=1);

namespace RubyVM\Stream;

class RubyVMBinaryStreamReader extends BinaryStreamReader implements RubyVMBinaryStreamReaderInterface
{
    use ResourceCreatable;

    public function __construct(private readonly BinaryStreamReaderInterface $reader)
    {
        parent::__construct(
            $reader->streamHandler(),
            $reader->endian(),
        );
    }

    public function duplication(): self
    {
        return new RubyVMBinaryStreamReader(
            new BinaryStreamReader(
                new StreamHandler(
                    $this->createResourceHandlerByStream(
                        $this->reader
                            ->streamHandler()
                            ->resource(),
                        $this->reader
                            ->streamHandler()
                            ->size(),
                    ),
                    $this->reader
                        ->streamHandler()
                        ->size(),
                ),
                $this->reader->endian(),
            ),
        );
    }

    /**
     * @see https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L11299
     */
    public function smallValue(): int
    {
        $offset = $this->pos();

        // Emulates: rb_popcount32(uint32_t x)
        $ntzInt32 = static function (int $x): int {
            $x = ~$x & ($x - 1);
            $x = ($x & 0x55555555) + ($x >> 1 & 0x55555555);
            $x = ($x & 0x33333333) + ($x >> 2 & 0x33333333);
            $x = ($x & 0x0F0F0F0F) + ($x >> 4 & 0x0F0F0F0F);
            $x = ($x & 0x001F001F) + ($x >> 8 & 0x001F001F);

            return ($x & 0x0000003F) + ($x >> 16 & 0x0000003F);
        };

        $c = $this->readAsUnsignedByte();

        $n = (($c & 1) !== 0)
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
