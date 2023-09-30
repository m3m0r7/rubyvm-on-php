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

        if ($obj instanceof \RubyVM\VM\Core\Runtime\RubyClass) {
            return static::nameBy($obj->entity);
        }

        $classNamePath = explode('\\', $obj::class);
        $name = $classNamePath[array_key_last($classNamePath)];

        return rtrim($name, '_');
    }

    public static function idBy(object $obj): string
    {
        return (string) spl_object_id($obj);
    }
}
