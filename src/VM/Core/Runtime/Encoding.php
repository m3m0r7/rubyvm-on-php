<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\EnumIntValueFindable;

enum Encoding: int
{
    use EnumIntValueFindable;

    case RUBY_ENCINDEX_ASCII_8BIT = 0;

    case RUBY_ENCINDEX_UTF_8 = 1;

    case RUBY_ENCINDEX_US_ASCII = 2;

    // preserved indexes
    case RUBY_ENCINDEX_UTF_16BE = 3;

    case RUBY_ENCINDEX_UTF_16LE = 4;

    case RUBY_ENCINDEX_UTF_32BE = 5;

    case RUBY_ENCINDEX_UTF_32LE = 6;

    case RUBY_ENCINDEX_UTF_16 = 7;

    case RUBY_ENCINDEX_UTF_32 = 8;

    case RUBY_ENCINDEX_UTF8_MAC = 9;

    // for old options of regexp
    case RUBY_ENCINDEX_EUC_JP = 10;

    case RUBY_ENCINDEX_Windows_31J = 11;
}
