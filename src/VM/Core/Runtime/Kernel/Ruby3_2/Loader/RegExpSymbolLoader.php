<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Essential\Symbol\RegExpSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;

class RegExpSymbolLoader implements SymbolLoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($this->offset->offset);

        $option = $reader->readAsByte();
        $sourceString = $this->kernel->findObject($reader->smallValue());

        assert($sourceString instanceof StringSymbol);

        return new RegExpSymbol(
            $sourceString,
            $option,
        );
    }
}
