<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

interface BinaryStreamReaderInterface
{
    public function read(int $bytes): string;

    public function char(): string;

    public function int(): int;

    public function long(): int;

    public function longLong(): int|float;

    public function double(): float;

    public function short(): int;

    public function byte(): int;

    public function unsignedInt(): int;

    public function unsignedLong(): int;

    public function unsignedLongLong(): int|float;

    public function unsignedShort(): int;

    public function unsignedByte(): int;

    public function string(): string;

    public function dryReadValue(int|SizeOf $bytesOrSize): int|string;

    public function pos(int $newPos = null): int;

    public function size(): ?int;

    public function dryPosTransaction(callable $callback): mixed;

    public function smallValue(): int;
}
