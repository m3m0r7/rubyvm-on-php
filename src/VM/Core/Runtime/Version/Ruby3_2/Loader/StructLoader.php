<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Structure\Range;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\NumberSymbol;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RubyVMException;

class StructLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $this->kernel->stream()->pos($this
            ->offset
            ->align(Range::structure())
            ->offset);

        $range = new Range($this->kernel->stream());

        $beginSymbol = $this->kernel
            ->findObject($range->begin)
            ->symbol;

        $endSymbol = $this->kernel
            ->findObject($range->end)
            ->symbol;

        if (!$beginSymbol instanceof NumberSymbol) {
            throw new RubyVMException(sprintf('The StructLoader expects NumberSymbol at a begin property when creating a range object but actual symbol is %s', get_class($beginSymbol)));
        }

        if (!$endSymbol instanceof NumberSymbol) {
            throw new RubyVMException(sprintf('The StructLoader expects NumberSymbol at a end property when creating a range object but actual symbol is %s', get_class($endSymbol)));
        }

        return new RangeSymbol(
            begin: $beginSymbol,
            end: $endSymbol,
            excludeEnd: 1 === $range->excl,
        );
    }
}
