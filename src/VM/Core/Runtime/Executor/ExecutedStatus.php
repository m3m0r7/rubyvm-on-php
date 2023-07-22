<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

enum ExecutedStatus
{
    case SUCCESS;

    case FAILED;

    case UNKNOWN;

    case IN_COMPLETED;
}
