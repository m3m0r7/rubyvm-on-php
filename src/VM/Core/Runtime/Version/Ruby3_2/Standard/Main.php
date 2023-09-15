<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\KernelInterface;
use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\RubyClassExtendable;
use RubyVM\VM\Core\Runtime\RubyClassImplementationInterface;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;

class Main implements MainInterface, RubyClassImplementationInterface
{
    use RubyClassExtendable;

    public function __construct(
        protected readonly KernelInterface $kernel,
        protected readonly DefinedClassEntries $definedClassEntries,
    ) {
        foreach ($this->definedClassEntries as $className => $definedClassEntry) {
            static::$userLandClasses[$className] = $definedClassEntry;
        }
    }

    public function puts(SymbolInterface $symbol): SymbolInterface
    {
        $string = '';
        if ($symbol instanceof ArraySymbol || $symbol instanceof RangeSymbol) {
            foreach ($symbol as $number) {
                $string .= "{$number}\n";
            }
        } elseif ($symbol instanceof NilSymbol) {
            // When an argument is a nil symbol, then displays a break only
            $string = "\n";
        } else {
            $string = (string) $symbol;
        }
        if (!str_ends_with($string, "\n")) {
            $string .= "\n";
        }

        $this->kernel->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return new NilSymbol();
    }

    public function phpinfo(): void
    {
        $this->kernel->IOContext()->stdOut->write('PHP Version: ' . PHP_VERSION . "\n");
    }

    public function exit(int $code = 0): void
    {
        exit($code);
    }
}
