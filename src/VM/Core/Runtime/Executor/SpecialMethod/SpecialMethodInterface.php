<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\SpecialMethod;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

interface SpecialMethodInterface
{
    public function process(RubyClassInterface $class, ContextInterface $context, CallInfoInterface $callInfo, mixed ...$arguments): mixed;
}
