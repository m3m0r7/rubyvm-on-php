<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Helper;

use RubyVM\VM\Exception\NotFoundEnumValueException;

trait EnumNameFindable
{
    public static function find(string $name): self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new NotFoundEnumValueException(sprintf('Unknown case name %s#%s', self::class, $name));
    }
}
