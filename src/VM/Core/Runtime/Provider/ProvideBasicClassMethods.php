<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Symbol\ArraySymbol;
use RubyVM\VM\Core\Runtime\Symbol\ID;
use RubyVM\VM\Core\Runtime\Symbol\NilSymbol;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Core\Runtime\Symbol\RangeSymbol;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\RubyVMException;

trait ProvideBasicClassMethods
{
    public function puts(Object_ $object): Object_
    {
        $symbol = $object->symbol;

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

        $this->context->kernel()->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return (new NilSymbol())
            ->toObject();
    }

    public function exit(int $code = 0): void
    {
        exit($code);
    }

    public function inspect(): SymbolInterface
    {
        $string = (string) $this;
        if ($this instanceof Object_) {
            $string = match(($this->symbol)::class) {
                StringSymbol::class => '"' . $string . '"',
                default => (string) $string,
            };
        }

        return new StringSymbol($string);
    }
}
