<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\OperationProcessorException;

class VoidSymbol implements SymbolInterface
{
    public function __construct() {}

    public function __toString(): string
    {
        return 'void';
    }

    public function valueOf(): null
    {
        return null;
    }

    public function toRubyClass(): RubyClass
    {
        return new RubyClass(
            info: new ObjectInfo(
                type: SymbolType::SYMBOL,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: $this,
        );
    }

    public function testValue(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }
}
