<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\YARV\Essential\Symbol\VoidSymbol;

#[BindAliasAs('Void')]
class Void_ extends Entity implements EntityInterface
{
    public function __construct(VoidSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null): self
    {
        return new self(new VoidSymbol());
    }
}
