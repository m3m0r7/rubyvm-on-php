<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\BinaryStreamReaderException;

class BinaryStreamReader implements BinaryStreamReaderInterface
{
    public function __construct(
        protected readonly StreamHandlerInterface $streamHandler,
        protected Endian $endian = Endian::LITTLE_ENDIAN
    ) {}

    public function streamHandler(): StreamHandlerInterface
    {
        return $this->streamHandler;
    }

    public function endian(): Endian
    {
        return $this->endian;
    }

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

    public function readAsString(): string
    {
        $string = '';

        do {
            $string .= $char = $this->streamHandler->read(1);
        } while ($char !== "\x00" && !$this->streamHandler->isTerminated());

        return rtrim($string, "\x00");
    }
}
