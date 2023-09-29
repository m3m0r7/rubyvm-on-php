<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

enum CheckMatchType: int
{
    case WHEN = 1;

    case CASE = 2;

    case RESCUE = 3;
};
