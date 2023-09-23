<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Insn;

use RubyVM\VM\Core\Helper\EnumStringValueFindable;

enum InsnType: string
{
    use EnumStringValueFindable;

    case TS_VARIABLE = '.';

    case TS_CALLDATA = 'C';

    case TS_CDHASH = 'H';

    case TS_IC = 'K';

    case TS_IVC = 'A';

    case TS_ICVARC = 'J';

    case TS_ID = 'I';

    case TS_ISE = 'T';

    case TS_ISEQ = 'S';

    case TS_OFFSET = 'O';

    case TS_VALUE = 'V';

    case TS_LINDEX = 'L';

    case TS_FUNCPTR = 'F';

    case TS_NUM = 'N';

    case TS_BUILTIN = 'R';
}
