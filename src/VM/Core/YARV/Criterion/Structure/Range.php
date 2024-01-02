<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Structure;

use RubyVM\Stream\SizeOf;

/**
 * @property int $classIndex
 * @property int $length
 * @property int $begin
 * @property int $end
 * @property int $excl
 */
class Range extends AbstractStructure implements StructureInterface
{
    /**
     * @return array<string, SizeOf>
     */
    public static function structure(): array
    {
        return [
            'classIndex' => SizeOf::UNSIGNED_LONG_LONG,
            'length' => SizeOf::UNSIGNED_LONG_LONG,
            'begin' => SizeOf::UNSIGNED_LONG_LONG,
            'end' => SizeOf::UNSIGNED_LONG_LONG,
            'excl' => SizeOf::INT,
        ];
    }
}
