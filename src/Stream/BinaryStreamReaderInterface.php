<?php

declare(strict_types=1);

namespace RubyVM\Stream;

interface BinaryStreamReaderInterface
{
    public function streamHandler(): StreamHandlerInterface;

    public function endian(): Endian;

    public function read(int $bytes): string;

    public function readAsChar(): string;

    public function readAsInt(): int;

    public function readAsLong(): int;

    public function readAsLongLong(): float|int;

    public function readAsDouble(): float;

    public function readAsShort(): int;

    public function readAsByte(): int;

    public function readAsUnsignedInt(): int;

    public function readAsUnsignedLong(): int;

    public function readAsUnsignedLongLong(): float|int;

    public function readAsUnsignedShort(): int;

    public function readAsUnsignedByte(): int;

    public function readAsString(): string;

    public function pos(int $newPos = null): int;

    public function size(): ?int;
}
