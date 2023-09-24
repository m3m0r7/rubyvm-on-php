<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ObjectInfo;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolType;
use RubyVM\VM\Exception\OperationProcessorException;

abstract class Entity implements EntityInterface
{
    protected SymbolInterface $symbol;

    public function toBeRubyClass(): RubyClassInterface
    {
        return new RubyClass(
            info: new ObjectInfo(
                type: SymbolType::findBySymbol($this->symbol),
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            entity: clone $this,
        );
    }

    public function __clone()
    {
        // Deep copy bound symbol
        $this->symbol = clone $this->symbol;
    }

    public function testValue(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }

    public function __toString()
    {
        return (string) $this->symbol;
    }

    public function symbol(): SymbolInterface
    {
        return $this->symbol;
    }

    public function valueOf(): mixed
    {
        return $this->symbol->valueOf();
    }
}
