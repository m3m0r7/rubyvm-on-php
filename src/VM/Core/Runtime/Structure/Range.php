<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Structure;

use RubyVM\VM\Stream\SizeOf;

/**
 * @property-read int $classIndex
 * @property-read int $length
 * @property-read int $begin
 * @property-read int $end
 * @property-read int $excl
 */
class Range extends AbstractStructure implements StructureInterface
{
    public static function structure(): array
    {
        return [
            'classIndex' => SizeOf::LONG_LONG,
            'length' => SizeOf::LONG_LONG,
            'begin' => SizeOf::LONG_LONG,
            'end' => SizeOf::LONG_LONG,
            'excl' => SizeOf::INT,
        ];
    }
}
