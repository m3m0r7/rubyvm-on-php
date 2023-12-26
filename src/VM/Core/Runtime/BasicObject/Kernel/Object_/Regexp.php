<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\BasicObject\SymbolizeInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\RegexpSymbol;

class Regexp extends Object_ implements RubyClassInterface, SymbolizeInterface
{
    use Symbolizable;

    public function __construct(RegexpSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null, int $option = null): self
    {
        return new self(new RegexpSymbol($value, $option));
    }

    public function __toString(): string
    {
        // TODO: Convert Ruby regexp to PCRE
        return '/' . $this->symbol->valueOf()->valueOf() . '/';
    }

    /**
     * @see https://docs.ruby-lang.org/ja/latest/class/Regexp.html#I_--3D--7E
     */
    #[BindAliasAs('=~')]
    public function equalsTilde(NilClass|String_ $source): Integer_|NilClass
    {
        if ($source instanceof NilClass) {
            return NilClass::createBy();
        }

        preg_match(
            (string) $this,
            (string) $source,
            $match,
            PREG_OFFSET_CAPTURE,
        );

        if ($match === []) {
            return NilClass::createBy();
        }

        [, $offset] = $match[0];

        return Integer_::createBy($offset);
    }
}
