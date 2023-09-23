<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

trait ProvideBasicClassMethods
{
    public function puts(RubyClass $object): RubyClass
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
            ->toRubyClass();
    }

    public function exit(int $code = 0): void
    {
        exit($code);
    }

    public function inspect(): SymbolInterface
    {
        $string = (string) $this;
        if ($this instanceof RubyClass) {
            $string = match (($this->symbol)::class) {
                StringSymbol::class => '"' . $string . '"',
                default => (string) $string,
            };
        }

        return new StringSymbol($string);
    }
}
