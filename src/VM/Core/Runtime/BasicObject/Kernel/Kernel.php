<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel;

use RubyVM\VM\Core\Runtime\BasicObject\BasicObject;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Exception\RubyVMException;

abstract class Kernel extends BasicObject
{
    public function __dir__(): String_
    {
        if (!$this->context instanceof \RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface) {
            throw new RubyVMException('A context is not injected');
        }

        return String_::createBy(
            $this->context
                ->instructionSequence()
                ->body()
                ->info()
                ->path(),
        );
    }
}
