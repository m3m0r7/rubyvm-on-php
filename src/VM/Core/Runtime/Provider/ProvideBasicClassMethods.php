<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Entity\Nil;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\RangeSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

trait ProvideBasicClassMethods
{
    public function puts(RubyClassInterface $object): RubyClassInterface
    {
        $symbol = $object->entity->symbol();

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

        $this->context->IOContext()->stdOut->write($string);

        // The puts returns (nil)
        return (new Nil(new NilSymbol()))
            ->toBeRubyClass();
    }

    public function exit(int $code = 0): void
    {
        exit($code);
    }

    public function inspect(): RubyClassInterface
    {
        $string = match (($this->entity->symbol())::class) {
            StringSymbol::class => '"' . ((string) $this) . '"',
            default => (string) $this,
        };

        return (new String_(new StringSymbol($string)))
            ->toBeRubyClass();
    }
}
