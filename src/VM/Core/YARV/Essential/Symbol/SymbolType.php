<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\EnumIntValueFindable;

enum SymbolType
{
    use EnumIntValueFindable;

    case NONE;

    case OBJECT;

    case CLASS_;

    case MODULE;

    case FLOAT;

    case STRING;

    case REGEXP;

    case ARRAY;

    case HASH;

    case STRUCT;

    case BIGNUM;

    case FILE;

    case DATA;

    case MATCH;

    case COMPLEX;

    case RATIONAL;

    case RSV_0x10; // TODO: Reserved case

    case NIL;

    case TRUE;

    case FALSE;

    case SYMBOL;

    case FIXNUM;

    case UNDEF;

    case RSV_0x17; // TODO: Reserved case

    case RSV_0x18; // TODO: Reserved case

    case RSV_0x19; // TODO: Reserved case

    case IMEMO;

    case NODE;

    case ICLASS;

    case ZOMBIE;

    case RSV_0x1e; // TODO: Reserved case

    case RSV_0x1f; // TODO: Reserved case

    public static function findBySymbol(SymbolInterface $symbol): self
    {
        return match ($symbol) {
            NumberSymbol::class => self::FIXNUM,
            StringSymbol::class => self::STRING,
            default => self::NONE,
        };
    }
}
