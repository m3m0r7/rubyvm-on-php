<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Exception\NotFoundInstanceMethod;

class Object_
{
    public readonly ID $id;

    public function __construct(
        public ObjectInfo $info,
        public SymbolInterface $symbol,
        public ?Offset $offset = null,
        ID $id = null
    ) {
        $this->id = $id ?? new ID($this);
    }

    public function __call(string $name, array $arguments)
    {
        return match ($name) {
            '^' => $this->calculateXOR(...$arguments),
            '**' => $this->calculatePower(...$arguments),
            default => throw new NotFoundInstanceMethod(
                sprintf(
                    'Not found instance method `%s`',
                    $name,
                ),
            ),
        };
    }

    public function calculateXOR(NumberSymbol $symbol): Object_
    {
        /**
         * @var NumberSymbol $symbol
         */
        $currentSymbol = $this->symbol;
        return new self(
            new ObjectInfo(
                type: SymbolType::FIXNUM,
                specialConst: 1,
                frozen: 1,
                internal: 0,
            ),
            new NumberSymbol(
                $currentSymbol->number ^ $symbol->number,
            ),
            null,
            $this->id,
        );
    }

    public function calculatePower(NumberSymbol $symbol): Object_
    {
        /**
         * @var NumberSymbol $currentSymbol
         */
        $currentSymbol = $this->symbol;
        return new self(
            new ObjectInfo(
                type: SymbolType::FIXNUM,
                specialConst: 1,
                frozen: 1,
                internal: 0,
            ),
            new NumberSymbol(
                $currentSymbol->number ** $symbol->number,
            ),
            null,
            $this->id,
        );
    }
}
