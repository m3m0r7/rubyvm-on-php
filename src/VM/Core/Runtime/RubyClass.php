<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Entity\Array_;
use RubyVM\VM\Core\Runtime\Entity\Boolean;
use RubyVM\VM\Core\Runtime\Entity\Number;
use RubyVM\VM\Core\Runtime\Entity\String_;
use RubyVM\VM\Core\Runtime\Essential\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\SpecialMethodCallerEntries;
use RubyVM\VM\Core\YARV\Criterion\Offset\Offset;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\ArraySymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\BooleanSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\NumberSymbol;
use RubyVM\VM\Core\YARV\Essential\Symbol\ObjectInfo;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Exception\NotFoundInstanceMethod;
use RubyVM\VM\Exception\SymbolUnsupportedException;

class RubyClass implements RubyClassInterface
{
    use ShouldBeRubyClass {
        __call as private callExtendedMethod;
    }

    public readonly ID $id;

    public function __construct(
        public ObjectInfo $info,
        public EntityInterface $entity,
        public ?Offset $offset = null,
        ID $id = null
    ) {
        $this->id = $id ?? new ID($this->entity->symbol());
    }

    public function __clone()
    {
        $this->entity = clone $this->entity;
    }

    public function __call(string $name, array $arguments)
    {
        try {
            $result = $this->callExtendedMethod($name, $arguments);
        } catch (NotFoundInstanceMethod $e) {
            if (method_exists($this->entity, $name)) {
                $result = $this->entity->{$name}(...$arguments);
            } else {
                if (isset(SpecialMethodCallerEntries::map()[$name])) {
                    // Do not throw the name is a special method in the entries
                    $result = clone $this->entity;
                } else {
                    throw new NotFoundInstanceMethod(sprintf(<<< '_'
                        Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.
                        Use try-catch statement and checking a previous exception via this exception if you want to solve kindly this problems.
                        _, /* Call to undefined method when not defined on symbol */ ClassHelper::nameBy($this->entity), $name), $e->getCode());
                }
            }
        }

        if ($result instanceof EntityInterface) {
            return $result->toRubyClass()
                ->setRuntimeContext($this->context)
                ->setUserlandHeapSpace($this->userlandHeapSpace);
        }

        return $result;
    }

    public static function initializeByClassName(string $className): RubyClassInterface
    {
        return (new $className(match ($className) {
            Array_::class => new ArraySymbol([]),
            String_::class => new StringSymbol(''),
            Boolean::class => new BooleanSymbol(true),
            Number::class => new NumberSymbol(0),
            default => throw new SymbolUnsupportedException(
                sprintf(
                    'The symbol cannot be instance - the symbol did not support initialize: %s',
                    $className,
                ),
            ),
        }))->toRubyClass();
    }

    public function __toString(): string
    {
        return (string) $this->entity;
    }
}
