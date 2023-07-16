<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class ClassHelper
{
    public static function nameBy(object $obj): string
    {
        $classNamePath = explode('\\', get_class($obj));

        return $classNamePath[array_key_last($classNamePath)];
    }

    public static function idBy(object $obj): string
    {
        return (string) spl_object_id($obj);
    }
}
