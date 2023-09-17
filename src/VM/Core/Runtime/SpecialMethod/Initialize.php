<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\SpecialMethod;

use RubyVM\VM\Core\Runtime\RubyClassExtendableInterface;

class Initialize implements SpecialMethodInterface
{
    public function process(RubyClassExtendableInterface $class, mixed ...$arguments): mixed
    {
        if ($class->hasMethod('initialize')) {
            $class->initialize(...$arguments);
        } else {
            return $class->new(...$arguments);
        }

        return $class;
    }
}
