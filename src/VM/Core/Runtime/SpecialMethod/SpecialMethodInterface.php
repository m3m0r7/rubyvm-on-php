<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\SpecialMethod;

use RubyVM\VM\Core\Runtime\RubyClassInterface;

interface SpecialMethodInterface
{
    public function process(RubyClassInterface $class, mixed ...$arguments): mixed;
}
