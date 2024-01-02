<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Essential;

use RubyVM\RubyVersion;
use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Option;

interface RubyVMInterface
{
    public function option(): Option;

    public function register(RubyVersion $rubyVersion, string $kernelClass): self;

    public function disassemble(RubyVersion $useVersion = null): ExecutorInterface;
}
