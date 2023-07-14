<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Exception\NotFoundEnumValueException;

trait EnumIntValueFindable
{
    public static function of(int $value): self
    {
        return self::cases()[$value] ?? throw new NotFoundEnumValueException(
            sprintf(
                'Unknown case value %s#%s',
                self::class,
                $value,
            ),
        );
    }
}
