<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Float_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Symbol;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Array_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable\Range;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Offset;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Regexp;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Undefined;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Void_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ClassSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RegExpSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\UndefinedSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;
use RubyVM\VM\Exception\EntityException;

class ClassCreator
{
    public static function createClassBySymbol(SymbolInterface $symbol): RubyClassInterface
    {
        return match ($symbol::class) {
            ArraySymbol::class => new Array_($symbol),
            BooleanSymbol::class => $symbol->valueOf()
                ? TrueClass::createBy()
                : FalseClass::createBy(),
            ClassSymbol::class => new Class_($symbol),
            FloatSymbol::class => new Float_($symbol),
            NilSymbol::class => new NilClass($symbol),
            NumberSymbol::class => new Integer_($symbol),
            OffsetSymbol::class => new Offset($symbol),
            RangeSymbol::class => new Range($symbol),
            StringSymbol::class => new String_($symbol),
            UndefinedSymbol::class => new Undefined($symbol),
            VoidSymbol::class => new Void_($symbol),
            SymbolSymbol::class => new Symbol($symbol),
            RegExpSymbol::class => new Regexp($symbol),
            default => throw new EntityException(
                sprintf(
                    'The specified entity was not implemented yet: %s',
                    ClassHelper::nameBy($symbol),
                )
            ),
        };
    }
}
