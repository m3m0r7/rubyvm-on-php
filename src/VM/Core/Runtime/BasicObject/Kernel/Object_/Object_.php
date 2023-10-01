<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Kernel;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

abstract class Object_ extends Kernel
{
    #[BindAliasAs('nil?')]
    public function isNil(CallInfoInterface $callInfo): TrueClass|FalseClass
    {
        return $this instanceof NilClass || $this->valueOf() === null
            ? TrueClass::createBy()
            : FalseClass::createBy();
    }
}
