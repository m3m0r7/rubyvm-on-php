<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

class ClassHelper
{
    public static function nameBy(mixed $obj): string
    {
        if (!is_object($obj)) {
            return gettype($obj);
        }
        $classNamePath = explode('\\', get_class($obj));

        return $classNamePath[array_key_last($classNamePath)];
    }

    public static function idBy(object $obj): string
    {
        return (string) spl_object_id($obj);
    }
}
