<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\SpecialMethod;

use RubyVM\VM\Core\Runtime\RubyClassExtendableInterface;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;

interface SpecialMethodInterface
{
    public function process(RubyClassExtendableInterface $class, mixed ...$arguments): mixed;
}
