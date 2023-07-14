<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Verification\Verifier;

class Runtime
{
    public function __construct(
        public readonly KernelInterface $kernel,
        public readonly Verifier $verifier,
    ) {}
}
