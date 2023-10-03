<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

interface RubyClassImplementationInterface
{
    public function puts(CallInfoInterface $callInfo, RubyClassInterface $object): RubyClassInterface;

    public function exit(CallInfoInterface $callInfo, int $code = 0): never;

    public function inspect(CallInfoInterface $callInfo): RubyClassInterface;

    public function p(CallInfoInterface $callInfo, RubyClassInterface $object): RubyClassInterface;

    public function lambda(CallInfoInterface $callInfo, ContextInterface $context): RubyClassInterface;

    public function raise(CallInfoInterface $callInfo, RubyClassInterface $string, RubyClassInterface $class): RubyClassInterface;
}
