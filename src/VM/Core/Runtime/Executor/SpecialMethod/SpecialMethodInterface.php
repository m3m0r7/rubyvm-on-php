<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;

interface SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, mixed ...$arguments): mixed;
}
