<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\RubyVersion;

interface RubyVMInterface
{
    public function option(): Option;

    public function register(RubyVersion $rubyVersion, string $kernelClass): self;

    public function disassemble(RubyVersion $useVersion = null): ExecutorInterface;
}
