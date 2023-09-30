<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Entity\Entityable;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\OffsetSymbol;

class Offset extends Object_ implements RubyClassInterface
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
