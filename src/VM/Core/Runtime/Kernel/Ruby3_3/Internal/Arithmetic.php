<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\Internal;

class Arithmetic
{
    final public const RUBY_FIXNUM_FLAG = 0x01;

    public static function isFixNum(int $value): bool
    {
        return (bool) ($value & static::RUBY_FIXNUM_FLAG);
    }

    /**
     * NOTE: refer INT2FIX implementation.
     *
     * @see https://github.com/ruby/ruby/blob/master/include/ruby/internal/arithmetic/long.h
     */
    public static function fix2int(int $value): int
    {
        return $value >> 1;
    }

    /**
     * NOTE: refer INT2FIX implementation.
     *
     * @see https://github.com/ruby/ruby/blob/master/include/ruby/internal/arithmetic/long.h
     */
    public static function int2fix(int $value): int
    {
        // NOTE: Add RUBY_FIXNUM_FLAG
        return ($value << 1) | static::RUBY_FIXNUM_FLAG;
    }
}
