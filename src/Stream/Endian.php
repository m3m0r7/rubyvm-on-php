<?php

declare(strict_types=1);

namespace RubyVM\Stream;

enum Endian
{
    case LITTLE_ENDIAN;

    case BIG_ENDIAN;
}
