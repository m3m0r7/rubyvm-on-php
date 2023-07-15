<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;

interface RubyVMInterface
{
    public function option(): Option;
    public function register(RubyVersion $rubyVersion, string $kernelClass): self;
    public function disassemble(RubyVersion $useVersion = null): ExecutorInterface;
}
