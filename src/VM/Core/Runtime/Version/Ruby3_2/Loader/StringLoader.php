<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Loader;

use RubyVM\VM\Core\Runtime\Encoding;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\Option;
use RubyVM\VM\Core\Runtime\Symbol\LoaderInterface;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RubyVMException;

class StringLoader implements LoaderInterface
{
    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly Offset $offset,
    ) {}

    public function load(): SymbolInterface
    {
        $this->kernel->stream()->pos($this->offset->offset);
        $encIndex = $this->kernel->stream()->smallValue();
        $len = $this->kernel->stream()->smallValue();

        // see: https://github.com/ruby/ruby/blob/2f603bc4/compile.c#L12567
        if ($encIndex > Option::RUBY_ENCINDEX_BUILTIN_MAX) {
            throw new RubyVMException('Not implemented yet in encIndex > RUBY_ENCINDEX_BUILTIN_MAX comparison');
        }

        return new StringSymbol(
            string: $this->kernel->stream()->read($len),
            encoding: Encoding::of($encIndex),
        );
    }
}
