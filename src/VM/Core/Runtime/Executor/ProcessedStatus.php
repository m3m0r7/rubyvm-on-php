<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

enum ProcessedStatus: int
{
    case SUCCESS = 0;

    case JUMPED = 10;

    case FAILED = 254;

    case FINISH = -1;

    case UNKNOWN = 255;
}
