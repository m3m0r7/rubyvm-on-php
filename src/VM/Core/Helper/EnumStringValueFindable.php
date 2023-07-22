<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Exception\NotFoundEnumValueException;

trait EnumStringValueFindable
{
    public static function of(string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw new NotFoundEnumValueException(sprintf('Unknown case value %s#%s', self::class, $value));
    }
}
