<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class SymbolLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {
    }
    public function load(): SymbolInterface
    {
        // NOTE: The SymbolLoader is same at StringLoader
        return (new StringLoader($this->kernel, $this->offset))
            ->load();
    }
}
