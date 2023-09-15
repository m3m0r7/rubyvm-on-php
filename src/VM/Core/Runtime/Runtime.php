<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Verification\Verifier;

class Runtime implements RuntimeInterface
{
    private bool $done = false;

    public function __construct(
        public readonly KernelInterface $kernel,
        public readonly Verifier $verifier,
    ) {
    }


    public function rubyVersion(): string
    {
        return sprintf(
            '%d.%d',
            $this->kernel->majorVersion(),
            $this->kernel->minorVersion(),
        );
    }

    public function rubyPlatform(): string
    {
        return $this->kernel->rubyPlatform();
    }

    public function extraData(): string
    {
        return $this->kernel->extraData();
    }

    public function setup(): void
    {
        if ($this->done) {
            return;
        }

        $this
            ->kernel
            ->setup();

        $this->done = true;
    }

    public function kernel(): KernelInterface
    {
        return $this->kernel;
    }
}
