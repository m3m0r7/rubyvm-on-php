<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

class LocalTableHelper
{
    /**
     * @see https://github.com/ruby/ruby/blob/ruby_3_2/yjit/src/codegen.rs#L1482
     */
    public static function computeLocalTableIndex(int $localTableSize, int $slotIndex, int $level = 0): int
    {
        return $slotIndex;
    }
}
