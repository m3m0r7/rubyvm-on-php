<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;

class Offset extends Entity implements EntityInterface
{
    public function __construct(OffsetSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = 0): EntityInterface
    {
        return new self(new OffsetSymbol($value));
    }
}
