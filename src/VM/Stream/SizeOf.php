<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\BinaryStreamReaderException;

enum SizeOf
{
    case BOOL;
    case CHAR;
    case BYTE;
    case SHORT;
    case LONG;
    case INT;
    case LONG_LONG;
    case DOUBLE;

    public function size(): int
    {
        return match ($this) {
            self::BOOL, self::CHAR, self::BYTE => 1,
            self::SHORT => 2,
            self::INT, self::LONG => 4,
            self::LONG_LONG, self::DOUBLE => 8,
            default => throw new BinaryStreamReaderException(
                sprintf(
                    'Unknown SizeOf type %s',
                    $this->name,
                ),
            ),
        };
    }

    public function mask(): int
    {
        return match ($this) {
            self::BOOL, self::CHAR, self::BYTE => 0xff,
            self::SHORT => 0xffff,
            self::INT, self::LONG => 0xffffffff,
            self::LONG_LONG, self::DOUBLE => 0xffffffffffffffff,
            default => throw new BinaryStreamReaderException(
                sprintf(
                    'Unknown SizeOf type %s',
                    $this->name,
                ),
            ),
        };
    }
}
