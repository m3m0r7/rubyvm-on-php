<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\RegExpSymbol;

class RegExp extends Entity implements EntityInterface
{
    public function __construct(RegExpSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public static function createBy(mixed $value = null, int $option = null): EntityInterface
    {
        return new self(new RegExpSymbol($value, $option));
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
    public function equalsTilde(CallInfoInterface $callInfo, String_|Nil $source): Number|Nil
    {
        if ($source instanceof Nil) {
            return Nil::createBy();
        }

        preg_match(
            (string) $this,
            (string) $source,
            $match,
            PREG_OFFSET_CAPTURE,
        );

        if ($match === []) {
            return Nil::createBy();
        }

        [, $offset] = $match[0];

        return Number::createBy($offset);
    }
}
