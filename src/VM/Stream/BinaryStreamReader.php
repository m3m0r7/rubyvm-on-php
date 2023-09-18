<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\BinaryStreamReaderException;

class BinaryStreamReader implements BinaryStreamReaderInterface
{
    public function __construct(
        public readonly StreamHandlerInterface $streamHandler,
        protected Endian $endian = Endian::LITTLE_ENDIAN
    ) {}

    public function readAsChar(): string
    {
        return chr($this->readAsUnsignedByte());
    }

    public function readAsInt(): int
    {
        $value = $this->readAsUnsignedInt();

        return $value - (
            ($value & SizeOf::INT->size()) > 0
            ? SizeOf::INT->mask() + 1
            : 0
        );
    }

    public function readAsLong(): int
    {
        $value = $this->readAsUnsignedLong();

        return $value - (
            ($value & SizeOf::LONG->size()) > 0
            ? SizeOf::LONG->mask() + 1
            : 0
        );
    }

    public function readAsLongLong(): int|float
    {
        $value = $this->readAsUnsignedLongLong();

        return $value - (
            ($value & SizeOf::LONG_LONG->size()) > 0
            ? SizeOf::LONG_LONG->mask() + 1
            : 0
        );
    }

    public function readAsDouble(): float
    {
        /*
         * FIXME: This code depend on the machine. We must fix non-depending on the machine.
         *
         * @see https://www.php.net/manual/en/function.pack.php
         */
        return $this->readWithEndian(
            littleEndian: 'e',
            bigEndian: 'E',
            bytes: SizeOf::DOUBLE,
        );
    }

    public function readAsShort(): int
    {
        $value = $this->readAsUnsignedShort();

        return $value - (
            ($value & SizeOf::SHORT->size()) > 0
            ? SizeOf::SHORT->mask() + 1
            : 0
        );
    }

    public function readAsByte(): int
    {
        $value = $this->readAsUnsignedByte();

        return $value - (
            ($value & SizeOf::BYTE->size()) > 0
            ? SizeOf::BYTE->mask() + 1
            : 0
        );
    }

    public function readAsUnsignedInt(): int
    {
        return $this->readAsUnsignedLong();
    }

    public function readAsUnsignedLong(): int
    {
        return $this->readWithEndian(
            littleEndian: 'V',
            bigEndian: 'N',
            bytes: SizeOf::UNSIGNED_LONG,
        );
    }

    public function readAsUnsignedLongLong(): int
    {
        return $this->readWithEndian(
            littleEndian: 'P',
            bigEndian: 'J',
            bytes: SizeOf::UNSIGNED_LONG_LONG,
        );
    }

    public function readAsUnsignedShort(): int
    {
        return $this->readWithEndian(
            littleEndian: 'v',
            bigEndian: 'n',
            bytes: SizeOf::UNSIGNED_SHORT,
        );
    }

    public function readAsUnsignedByte(): int
    {
        return $this->readWithEndian(
            littleEndian: 'C',
            bigEndian: 'C',
            bytes: SizeOf::UNSIGNED_BYTE,
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

    private function readWithEndian(string $littleEndian, string $bigEndian, SizeOf $bytes): int|float
    {
        $read = unpack(
            Endian::LITTLE_ENDIAN === $this->endian
                ? $littleEndian
                : $bigEndian,
            $this->streamHandler->read($bytes->size()),
        );
        if (false === $read) {
            throw new BinaryStreamReaderException(sprintf('Cannot unpack from binary stream with %s', Endian::LITTLE_ENDIAN === $this->endian ? $littleEndian : $bigEndian));
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

    public function dryReadValue(int|SizeOf $bytesOrSize): int|string
    {
        $pos = $this->streamHandler->pos();

        try {
            if (is_int($bytesOrSize)) {
                return $this->read($bytesOrSize);
            }

            return match ($bytesOrSize) {
                SizeOf::BOOL, SizeOf::BYTE => $this->readAsByte(),
                SizeOf::CHAR => $this->readAsChar(),
                SizeOf::SHORT => $this->readAsShort(),
                SizeOf::INT => $this->readAsInt(),
                SizeOf::LONG => $this->readAsLong(),
                SizeOf::LONG_LONG => $this->readAsLongLong(),
                SizeOf::UNSIGNED_BYTE => $this->readAsUnsignedByte(),
                SizeOf::UNSIGNED_SHORT => $this->readAsUnsignedShort(),
                SizeOf::UNSIGNED_INT => $this->readAsUnsignedInt(),
                SizeOf::UNSIGNED_LONG => $this->readAsUnsignedLong(),
                SizeOf::UNSIGNED_LONG_LONG => $this->readAsUnsignedLongLong(),
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

    public function readAsString(): string
    {
        $string = '';

        do {
            $string .= $char = $this->streamHandler->read(1);
        } while ($char !== "\x00" && !$this->streamHandler->isTerminated());

        return rtrim($string, "\x00");
    }
}
