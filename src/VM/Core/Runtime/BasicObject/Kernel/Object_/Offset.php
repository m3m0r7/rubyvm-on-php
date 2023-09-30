<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;

class Offset implements EntityInterface
{
    use Entityable;

    public function __construct(OffsetSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = 0): self
    {
        return new self(new OffsetSymbol($value));
    }
}
