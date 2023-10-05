<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;

interface RubyClassImplementationInterface
{
    public function puts(RubyClassInterface $object): RubyClassInterface;

    public function exit(RubyClassInterface $code = null): never;

    public function inspect(): RubyClassInterface;

    public function p(RubyClassInterface $object): RubyClassInterface;

    public function lambda(ContextInterface $context): RubyClassInterface;

    public function raise(RubyClassInterface $string, RubyClassInterface $class): RubyClassInterface;

    public function send(string $name, CallInfoInterface $callInfo, RubyClassInterface|ContextInterface ...$arguments): ExecutedResult|RubyClassInterface;
}
