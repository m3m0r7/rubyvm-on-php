<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Kernel;

abstract class Object_ extends Kernel
{
    #[BindAliasAs('nil?')]
    public function isNil(): FalseClass|TrueClass
    {
        return $this instanceof NilClass || $this->valueOf() === null
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }
}
