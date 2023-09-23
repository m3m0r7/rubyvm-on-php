<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\YARV\Criterion\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\LoaderInterface;
use RubyVM\VM\Core\YARV\Criterion\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;

class ArrayLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($this->offset->offset);
        $len = $reader->smallValue();
        $array = [];

        for ($i = 0; $i < $len; ++$i) {
            $array[] = $this->kernel
                ->findObject(
                    $reader
                        ->smallValue()
                )
                ->symbol;
        }

        return new ArraySymbol($array);
    }
}
