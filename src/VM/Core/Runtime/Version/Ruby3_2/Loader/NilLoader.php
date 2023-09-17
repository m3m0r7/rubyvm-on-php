<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class NilLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $this->kernel->stream()->pos(
            $this->offset->offset,
        );
        $value = $this->kernel->stream()->smallValue();

        return new NilSymbol(
            $value,
        );
    }
}
