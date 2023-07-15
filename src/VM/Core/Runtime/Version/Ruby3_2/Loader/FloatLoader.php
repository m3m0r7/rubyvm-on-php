<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\FloatSymbol;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal\Arithmetic;
use RubyVM\VM\Stream\SizeOf;

class FloatLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {
    }
    public function load(): SymbolInterface
    {
        $this->kernel->stream()->pos(
            $this->offset
                ->align(SizeOf::DOUBLE)
                ->offset
        );
        $value = $this->kernel->stream()->double();

        return new FloatSymbol(
            $value,
        );
    }
}
