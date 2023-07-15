<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal;

class Arithmetic
{
    const RUBY_FIXNUM_FLAG = 0x01;

    /**
     * NOTE: refer INT2FIX implementation
     *
     * @see https://github.com/ruby/ruby/blob/master/include/ruby/internal/arithmetic/long.h
     */
    public static function fix2int(int $value): int
    {
        // NOTE: no needing `& ~static::RUBY_FIXNUM_FLAG` mask because the bit will lost when shifting to right
        return $value >> 1;
    }

    /**
     * NOTE: refer INT2FIX implementation
     *
     * @see https://github.com/ruby/ruby/blob/master/include/ruby/internal/arithmetic/long.h
     */
    public static function int2fix(int $value): int
    {
        // NOTE: Add RUBY_FIXNUM_FLAG
        return ($value << 1) | static::RUBY_FIXNUM_FLAG;
    }
}
