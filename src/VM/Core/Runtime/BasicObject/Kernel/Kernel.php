<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel;

use RubyVM\VM\Core\Runtime\BasicObject\BasicObject;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;

abstract class Kernel extends BasicObject
{
    public function __dir__(): String_
    {
        return String_::createBy(
            $this->context
                ->instructionSequence()
                ->body()
                ->info()
                ->path(),
        );
    }
}
