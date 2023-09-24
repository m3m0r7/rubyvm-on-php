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

    case UNSIGNED_BYTE;

    case UNSIGNED_SHORT;

    case UNSIGNED_LONG;

    case UNSIGNED_INT;

    case UNSIGNED_LONG_LONG;

    public function size(): int
    {
        return match ($this) {
            self::BOOL, self::CHAR, self::BYTE, self::UNSIGNED_BYTE => 1,
            self::SHORT, self::UNSIGNED_SHORT => 2,
            self::INT, self::LONG, self::UNSIGNED_INT, self::UNSIGNED_LONG => 4,
            self::LONG_LONG, self::UNSIGNED_LONG_LONG, self::DOUBLE => 8,
        };
    }

    public function mask(): int
    {
        return match ($this) {
            self::BOOL, self::CHAR, self::BYTE, self::UNSIGNED_BYTE => 0xFF,
            self::SHORT, self::UNSIGNED_SHORT => 0xFFFF,
            self::INT, self::LONG, self::UNSIGNED_INT, self::UNSIGNED_LONG => 0xFFFFFFFF,
            self::LONG_LONG, self::UNSIGNED_LONG_LONG, self::DOUBLE => throw new BinaryStreamReaderException('The PHP cannot mask big integer'),
        };
    }
}
