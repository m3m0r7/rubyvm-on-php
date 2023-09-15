<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ExecutorInterface;

interface RuntimeInterface
{
    public function rubyVersion(): string;

    public function rubyPlatform(): string;

    public function extraData(): string;

    public function setup(): void;

    public function kernel(): KernelInterface;
}
