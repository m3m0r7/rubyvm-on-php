<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\ClassCreator;
use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;

class ArraySymbolLoader implements SymbolLoaderInterface
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
            $array[] = ClassCreator::createClassBySymbol(
                $this->kernel
                    ->findObject(
                        $reader
                            ->smallValue()
                    ),
            );
        }

        return new ArraySymbol($array);
    }
}
