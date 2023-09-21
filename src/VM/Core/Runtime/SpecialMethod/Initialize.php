<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\SpecialMethod;

use RubyVM\VM\Core\Runtime\RubyClassExtendableInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;

class Initialize implements SpecialMethodInterface
{
    public function process(RubyClassInterface $class, mixed ...$arguments): mixed
    {
        if ($class->hasMethod('initialize')) {
            $class->initialize(...$arguments);
        } else {
            return $class->new(...$arguments);
        }

        return $class;
    }
}
