<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Essential\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\OperationProcessorException;

class FloatSymbol implements SymbolInterface
{
    public function __construct(
        private readonly float $number,
    ) {}

    public function valueOf(): float
    {
        return $this->number;
    }

    public function __toString(): string
    {
        $hasFraction = str_contains((string) $this->number, '.');
        if ($hasFraction) {
            return (string) rtrim(
                sprintf(
                    '%.16f',
                    $this->number
                ),
                '0'
            );
        }

        return "{$this->number}.0";
    }

    public function toRubyClass(): RubyClass
    {
        return new RubyClass(
            info: new ObjectInfo(
                type: SymbolType::FLOAT,
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
