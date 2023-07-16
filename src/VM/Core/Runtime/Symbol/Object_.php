<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
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
            throw new NotFoundInstanceMethod(
                sprintf(
                    <<< _
                    Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.
                    Use try-catch statement and checking a previous exception via this exception if you want to solve kindly this problems.
                    _,
                    // Call to undefined method when not defined on symbol
                    ClassHelper::nameBySymbol($this->symbol),
                    $name,
                ),
                $e->getCode(),
                $e,
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
