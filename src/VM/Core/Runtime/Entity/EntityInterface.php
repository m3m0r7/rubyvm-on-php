<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Runtime\Essential\RubyClassifiable;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

interface EntityInterface extends RubyClassifiable, \Stringable
{
    public function testValue(): bool;

    public function symbol(): SymbolInterface;

    public function valueOf(): mixed;

    public static function createBy(mixed $value): self;
}
