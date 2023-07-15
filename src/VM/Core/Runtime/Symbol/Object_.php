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
        $result = match ($name) {
            '^' => $this->symbol->calculateXOR(...$arguments),
            '**' => $this->symbol->calculatePower(...$arguments),
            '>>' => $this->symbol->calculateRightShift(...$arguments),
            'to_int' => $this->symbol->toInt(...$arguments),
            default => throw new NotFoundInstanceMethod(
                sprintf(
                    'Not found instance method `%s`',
                    $name,
                ),
            ),
        };

        return new Object_(
            new ObjectInfo(
                type: SymbolType::findBySymbol($result),
                specialConst: 0,
                frozen: 1,
                internal: 0,
            ),
            $result,
            null,
            $this->id,
        );
    }
}
