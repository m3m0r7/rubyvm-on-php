<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\SpecialMethodCallerEntries;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ID;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ObjectInfo;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;
use RubyVM\VM\Exception\NotFoundInstanceMethod;
use RubyVM\VM\Exception\SymbolUnsupportedException;

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
                if (isset(SpecialMethodCallerEntries::map()[$name])) {
                    // Do not throw the name is a special method in the entries
                    $result = clone $this->symbol;
                } else {
                    throw new NotFoundInstanceMethod(sprintf(<<< '_'
                        Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.
                        Use try-catch statement and checking a previous exception via this exception if you want to solve kindly this problems.
                        _, /* Call to undefined method when not defined on symbol */ ClassHelper::nameBy($this->symbol), $name), $e->getCode());
                }
            }
        }

        if ($result instanceof SymbolInterface) {
            return $result->toObject()
                ->setRuntimeContext($this->context)
                ->setUserlandHeapSpace($this->userlandHeapSpace);
        }

        return $result;
    }

    public static function initializeByClassName(string $className): Object_
    {
        return (new $className(match ($className) {
            ArraySymbol::class => [],
            StringSymbol::class => '',
            BooleanSymbol::class => true,
            NumberSymbol::class => 0,
            default => throw new SymbolUnsupportedException('The symbol cannot be instance - the symbol did not support initialize'),
        }))->toObject();
    }

    public function __toString(): string
    {
        return (string) $this->symbol;
    }
}
