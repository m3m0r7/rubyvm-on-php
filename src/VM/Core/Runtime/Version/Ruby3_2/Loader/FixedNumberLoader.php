<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Internal\Arithmetic;

class FixedNumberLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($this->offset->offset);
        $value = $reader->smallValue();

        return new NumberSymbol(
            Arithmetic::fix2int($value),
        );
    }
}
