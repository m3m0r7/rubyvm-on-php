<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Helper\EnumIntValueFindable;

enum VMSpecialObjectType: int
{
    use EnumIntValueFindable;

    case __ZERO = 0;

    case VM_SPECIAL_OBJECT_VMCORE = 1;

    case VM_SPECIAL_OBJECT_CBASE = 2;

    case VM_SPECIAL_OBJECT_CONST_BASE = 3;
}
