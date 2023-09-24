<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ClassSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\UndefinedSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;
use RubyVM\VM\Exception\EntityException;

class EntityHelper
{
    public static function createEntityBySymbol(SymbolInterface $symbol): EntityInterface
    {
        return match ($symbol::class) {
            ArraySymbol::class => new Array_($symbol),
            BooleanSymbol::class => new Boolean_($symbol),
            ClassSymbol::class => new Class_($symbol),
            FloatSymbol::class => new Float_($symbol),
            NilSymbol::class => new Nil($symbol),
            NumberSymbol::class => new Number($symbol),
            OffsetSymbol::class => new Offset($symbol),
            RangeSymbol::class => new Range($symbol),
            StringSymbol::class => new String_($symbol),
            UndefinedSymbol::class => new Undefined($symbol),
            VoidSymbol::class => new Void_($symbol),
            default => throw new EntityException(
                sprintf(
                    'The specified entity was not implemented yet: %s',
                    ClassHelper::nameBy($symbol),
                )
            ),
        };
    }
}
