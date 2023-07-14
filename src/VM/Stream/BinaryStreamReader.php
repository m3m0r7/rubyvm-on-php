<?php
declare(strict_types=1);
namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\BinaryStreamReaderException;
use RubyVM\VM\Exception\RubyVMException;

class BinaryStreamReader implements BinaryStreamReaderInterface
{
    public function __construct(
        public readonly StreamHandlerInterface $streamHandler,
        protected Endian $endian = Endian::LITTLE_ENDIAN
    ) {
    }

    public function char(): string
    {
        return chr($this->unsignedByte());
    }

    public function int(): int
    {
        $value = $this->unsignedInt();
        return $value - (($value & SizeOf::INT->size()) > 0
            ? SizeOf::INT->mask() + 1
            : 0
        );
    }

    public function long(): int
    {
        $value = $this->unsignedLong();
        return $value - (($value & SizeOf::LONG->size()) > 0
            ? SizeOf::LONG->mask() + 1
            : 0
        );
    }

    public function longLong(): int
    {
        $value = $this->unsignedLongLong();
        return $value - (($value & SizeOf::LONG_LONG->size()) > 0
            ? SizeOf::LONG_LONG->mask() + 1
            : 0
        );
    }

    public function short(): int
    {
        $value = $this->unsignedShort();
        return $value - (($value & SizeOf::SHORT->size()) > 0
            ? SizeOf::SHORT->mask() + 1
            : 0
        );
    }

    public function byte(): int
    {
        $value = $this->unsignedByte();
        return $value - (($value & SizeOf::BYTE->size()) > 0
            ? SizeOf::BYTE->mask() + 1
            : 0
        );
    }

    public function unsignedInt(): int
    {
        return $this->unsignedLong();
    }

    public function unsignedLong(): int
    {
        return $this->readWithEndian(
            littleEndian: 'V',
            bigEndian: 'N',
            bytes: SizeOf::LONG,
        );
    }

    public function unsignedLongLong(): int
    {
        return $this->readWithEndian(
            littleEndian: 'P',
            bigEndian: 'J',
            bytes: SizeOf::LONG_LONG,
        );
    }

    public function unsignedShort(): int
    {
        return $this->readWithEndian(
            littleEndian: 'v',
            bigEndian: 'n',
            bytes: SizeOf::SHORT,
        );
    }

    public function unsignedByte(): int
    {
        return $this->readWithEndian(
            littleEndian: 'C',
            bigEndian: 'C',
            bytes: SizeOf::BYTE,
        );
    }

    public function pos(int $newPos = null): int
    {
        return $this->streamHandler->pos($newPos);
    }

    public function read(int $bytes): string
    {
        return $this->streamHandler->read($bytes);
    }

    private function readWithEndian(string $littleEndian, string $bigEndian, SizeOf $bytes): int
    {
        $read = unpack(
            $this->endian === Endian::LITTLE_ENDIAN
                ? $littleEndian
                : $bigEndian,
            $this->streamHandler->read($bytes->size()),
        );
        if ($read === false) {
            throw new BinaryStreamReaderException(
                sprintf(
                    'Cannot unpack from binary stream with %s',
                    $this->endian === Endian::LITTLE_ENDIAN
                        ? $littleEndian
                        : $bigEndian,
                ),
            );
        }
        return $read[array_key_first($read)];
    }

    public function size(): ?int
    {
        return $this->streamHandler->size();
    }

    public function dryPosTransaction(callable $callback): mixed
    {
        $currentPos = $this->pos();
        try {
            return $callback($this);
        } finally {
            $this->pos($currentPos);
        }
    }

    public function dryReadUnsignedValue(int|SizeOf $bytesOrSize): int|string
    {
        $pos = $this->streamHandler->pos();
        try {
            if (is_int($bytesOrSize)) {
                return $this->read($bytesOrSize);
            }
            return match ($bytesOrSize) {
                SizeOf::BOOL, SizeOf::BYTE => $this->unsignedByte(),
                SizeOf::CHAR => $this->char(),
                SizeOf::INT => $this->unsignedInt(),
                SizeOf::LONG => $this->unsignedLong(),
                SizeOf::LONG_LONG => $this->unsignedLongLong(),
                default => throw new BinaryStreamReaderException('Unknown sizeof type'),
            };
        } finally {
            $this->streamHandler->pos($pos);
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
            $x = ~$x & ($x-1);
            $x = ($x & 0x55555555) + ($x >> 1 & 0x55555555);
            $x = ($x & 0x33333333) + ($x >> 2 & 0x33333333);
            $x = ($x & 0x0f0f0f0f) + ($x >> 4 & 0x0f0f0f0f);
            $x = ($x & 0x001f001f) + ($x >> 8 & 0x001f001f);
            $x = ($x & 0x0000003f) + ($x >>16 & 0x0000003f);
            return $x;
        };

        $c = $this->unsignedByte();

        $n = ($c & 1)
            ? 1
            : ($c == 0 ? 9 : $ntzInt32($c) + 1);

        $x = $c >> $n;

        if ($x === 0x7f) {
            $x = 1;
        }
        for ($i = 1; $i < $n; $i++) {
            $byte = $this->dryReadUnsignedValue(SizeOf::BYTE);
            if (is_string($byte)) {
                throw new RubyVMException(
                    sprintf(
                        'Unexpected read a small value (read stream is expecting a byte but got a string)'
                    ),
                );
            }
            $x <<= 8;
            $x |= $byte;
        }

        $this->pos(
            $offset + $n,
        );

        return $x;
    }
}
