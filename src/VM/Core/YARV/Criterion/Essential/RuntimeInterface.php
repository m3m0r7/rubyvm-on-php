<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion\Essential;

interface RuntimeInterface
{
    public function rubyVersion(): string;

    public function rubyPlatform(): string;

    public function extraData(): string;

    public function setup(): void;

    public function kernel(): KernelInterface;
}
