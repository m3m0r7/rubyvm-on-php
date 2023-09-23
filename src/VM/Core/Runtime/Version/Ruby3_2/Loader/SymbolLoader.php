<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\YARV\Criterion\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\LoaderInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;

class SymbolLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        // NOTE: The SymbolLoader is same at StringLoader
        return (new StringLoader($this->kernel, $this->offset))
            ->load();
    }
}
