<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Criterion\Structure\Range;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;
use RubyVM\VM\Exception\RubyVMException;

class StructSymbolLoader implements SymbolLoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($this
            ->offset
            ->align(Range::structure())
            ->offset);

        $range = new Range($reader);

        $beginSymbol = $this->kernel
            ->findObject($range->begin);

        $endSymbol = $this->kernel
            ->findObject($range->end);

        if (!$beginSymbol instanceof NumberSymbol) {
            throw new RubyVMException(sprintf('The StructSymbolLoader expects NumberSymbol at a begin property when creating a range object but actual symbol is %s', $beginSymbol::class));
        }

        if (!$endSymbol instanceof NumberSymbol) {
            throw new RubyVMException(sprintf('The StructSymbolLoader expects NumberSymbol at a end property when creating a range object but actual symbol is %s', $endSymbol::class));
        }

        return new RangeSymbol(
            begin: $beginSymbol,
            end: $endSymbol,
            excludeEnd: 1 === $range->excl,
        );
    }
}
