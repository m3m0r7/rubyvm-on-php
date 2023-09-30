<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Entity;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait Entityable
{
    protected SymbolInterface $symbol;

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
