<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolize;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\FloatSymbol;

#[BindAliasAs('Float')]
class Float_ extends Comparable implements RubyClassInterface, Symbolize
{
    use Symbolizable;

    public function __construct(FloatSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = 0): self
    {
        return new self(new FloatSymbol($value));
    }
}
