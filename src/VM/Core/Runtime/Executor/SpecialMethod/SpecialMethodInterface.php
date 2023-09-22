<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\RubyClassInterface;

interface SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, mixed ...$arguments): mixed;
}
