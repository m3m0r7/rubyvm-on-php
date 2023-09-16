<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Option;

class LocalTableHelper
{
    /**
     * @see https://github.com/ruby/ruby/blob/ruby_3_2/yjit/src/codegen.rs#L1482
     */
    public static function computeLocalTableIndex(int $localTableSize, int $slotIndex, int $level = 0): int
    {
        // $op = $slotIndex - Option::VM_ENV_DATA_SIZE;
        // $localTableIndex = $localTableSize - $op - 1;
        return $slotIndex;
    }
}
