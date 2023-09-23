<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Essential\Encoding;
use RubyVM\VM\Core\YARV\Essential\KernelInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolLoaderInterface;
use RubyVM\VM\Exception\RubyVMException;

class StringSymbolLoader implements SymbolLoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $reader = $this->kernel->stream()->duplication();
        $reader->pos($this->offset->offset);
        $encIndex = $reader->smallValue();
        $len = $reader->smallValue();

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L12567
        if ($encIndex > Option::RUBY_ENCINDEX_BUILTIN_MAX) {
            throw new RubyVMException('Not implemented yet in encIndex > RUBY_ENCINDEX_BUILTIN_MAX comparison');
        }

        return new StringSymbol(
            string: $reader->read($len),
            encoding: Encoding::of($encIndex),
        );
    }
}
