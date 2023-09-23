<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\OperationProcessorException;

class ClassSymbol implements SymbolInterface
{
    public function __construct(
        private StringSymbol $class,
    ) {}

    public function valueOf(): string
    {
        return $this->class->valueOf();
    }

    public function toRubyClass(): RubyClass
    {
        return new RubyClass(
            info: new ObjectInfo(
                type: SymbolType::CLASS_,
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            symbol: clone $this,
        );
    }

    public function __toString(): string
    {
        return $this->valueOf();
    }

    public function isTestable(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }
}
