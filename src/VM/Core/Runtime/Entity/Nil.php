<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\NilSymbol;

class Nil extends Entity implements EntityInterface
{
    public function __construct(NilSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function testValue(): bool
    {
        // Always return false
        return false;
    }

    public static function createBy(mixed $value = null): self
    {
        static $symbol = new NilSymbol();

        return new self($symbol);
    }

    public function toBeRubyClass(): RubyClassInterface
    {
        static $class = null;

        return $class ??= parent::toBeRubyClass();
    }
}
