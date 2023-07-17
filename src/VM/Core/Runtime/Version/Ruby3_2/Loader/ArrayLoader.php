<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class ArrayLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {
    }
    public function load(): SymbolInterface
    {
        $this->kernel->stream()->pos($this->offset->offset);
        $len = $this->kernel->stream()->smallValue();
        $array = [];

        for ($i = 0; $i < $len; $i++) {
            $array[] = $this->kernel
                ->findObject(
                    $this->kernel
                        ->stream()
                        ->smallValue()
                )
                ->symbol;
        }

        return new ArraySymbol($array);
    }
}
