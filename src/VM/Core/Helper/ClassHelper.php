<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\Symbol\Object_;

class ClassHelper
{
    public static function nameBy(mixed $obj): string
    {
        if (!is_object($obj)) {
            return gettype($obj);
        }
        if (get_class($obj) === Object_::class) {
            return 'Object';
        }
        $classNamePath = explode('\\', get_class($obj));

        return $classNamePath[array_key_last($classNamePath)];
    }

    public static function idBy(object $obj): string
    {
        return (string) spl_object_id($obj);
    }
}
