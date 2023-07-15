<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\DefaultInstanceMethodEntries;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;
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
        $defaultMethodEntries = new DefaultInstanceMethodEntries();

        try {
            /**
             * @var InstanceMethodInterface|null $entry
             */
            $entry = $defaultMethodEntries[$name] ?? null;

            if ($entry === null) {
                throw new \Error();
            }

            $result = $entry->process($this->symbol, ...$arguments);
        } catch (\Error $e) {
            // Call to undefined method when not defined on symbol
            $classNamePath = explode('\\', get_class($this->symbol));

            throw new NotFoundInstanceMethod(
                sprintf(
                    'Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.',
                    $classNamePath[array_key_last($classNamePath)],
                    $name,
                ),
            );
        }

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
