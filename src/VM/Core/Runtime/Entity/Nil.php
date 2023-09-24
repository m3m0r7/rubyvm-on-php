<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;

class Nil extends Entity implements EntityInterface
{
    public function __construct(NilSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null): EntityInterface
    {
        return new self(new NilSymbol());
    }
}
