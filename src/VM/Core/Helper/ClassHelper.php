<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Core\Runtime\ExtendedClassEntry;
use RubyVM\VM\Core\Runtime\Symbol\Object_;

class ClassHelper
{
    public static function nameBy(mixed $obj): string
    {
        if (!is_object($obj)) {
            return gettype($obj);
        }
        if (Object_::class === get_class($obj)) {
            return static::nameBy($obj->symbol);
        }
        if (ExtendedClassEntry::class === get_class($obj)) {
            $obj = $obj->className;
        }
        $classNamePath = explode('\\', is_object($obj) ? get_class($obj) : $obj);

        return $classNamePath[array_key_last($classNamePath)];
    }

    public static function idBy(object $obj): string
    {
        return (string) spl_object_id($obj);
    }
}
