<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\OperationProcessorException;

class OffsetSymbol implements SymbolInterface
{
    public function __construct(
        public readonly int $offset,
    ) {}

    public function __toString(): string
    {
        return (string) $this->offset;
    }

    public function valueOf(): int
    {
        return $this->offset;
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

    public function bindAlias(): array
    {
        return [];
    }

    public function isTestable(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }
}
