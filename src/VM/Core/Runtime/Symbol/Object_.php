<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Helper\DefaultInstanceMethodEntries;
use RubyVM\VM\Core\Runtime\Executor\InstanceMethodInterface;
use RubyVM\VM\Core\Runtime\Offset\Offset;
use RubyVM\VM\Core\Runtime\RubyClassInterface;
use RubyVM\VM\Core\Runtime\ShouldBeRubyClass;
use RubyVM\VM\Exception\NotFoundInstanceMethod;

class Object_ implements RubyClassInterface
{
    use ShouldBeRubyClass {
        __call as private callExtendedMethod;
    }

    public readonly ID $id;

    public function __construct(
        public ObjectInfo $info,
        public SymbolInterface $symbol,
        public ?Offset $offset = null,
        ID $id = null
    ) {
        $this->id = $id ?? new ID($this);
    }

    public function __clone()
    {
        $this->symbol = clone $this->symbol;
    }

    public function __call(string $name, array $arguments)
    {
        try {
            $result = $this->callExtendedMethod($name, $arguments);
        } catch (NotFoundInstanceMethod $e) {
            if (method_exists($this->symbol, $name)) {
                $result = $this->symbol->{$name}(...$arguments);
            } else {
                throw new NotFoundInstanceMethod(sprintf(<<< '_'
                        Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.
                        Use try-catch statement and checking a previous exception via this exception if you want to solve kindly this problems.
                        _, /* Call to undefined method when not defined on symbol */ ClassHelper::nameBy($this->symbol), $name), $e->getCode());
            }
        }

        return $result;
    }

    public function __toString(): string
    {
        return (string) $this->symbol;
    }
}
