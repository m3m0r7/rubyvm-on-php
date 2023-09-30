<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\String_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Executor\Operation\SpecialMethodCallerEntries;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Exception\NotFoundInstanceMethod;

abstract class BasicObject implements RubyClassInterface
{
    use ShouldBeRubyClass {
        __call as private callExtendedMethod;
    }

    protected ?Class_ $entity = null;

    public function className(): string
    {
        $classNames = explode('\\', static::class);

        return array_pop($classNames);
    }

    #[BindAliasAs('to_s')]
    public function toString(): String_
    {
        return String_::createBy(
            (string) $this,
        );
    }

    public function __toString(): string
    {
        return $this->className();
    }

    /**
     * @param (ContextInterface|RubyClass)[] $arguments
     */
    public function __call(string $name, array $arguments): ExecutedResult|RubyClassInterface|null
    {
        try {
            $result = $this->callExtendedMethod($name, $arguments);
        } catch (NotFoundInstanceMethod $e) {
            if (method_exists($this, $name)) {
                $result = $this->{$name}(...$arguments);
            } elseif (isset(SpecialMethodCallerEntries::map()[$name])) {
                // Do not throw the name is a special method in the entries
                $result = clone $this;
            } else {
                throw new NotFoundInstanceMethod(sprintf(<<< '_'
                        Not found instance method %s#%s. In the actually, arguments count are unmatched or anymore problems when throwing this exception.
                        Use try-catch statement and checking a previous exception via this exception if you want to solve kindly this problems.
                        _, /* Call to undefined method when not defined on symbol */ ClassHelper::nameBy($this), $name), $e->getCode());
            }
        }

        if ($result instanceof ExecutedResult) {
            if ($result->threw instanceof \Throwable) {
                throw $result->threw;
            }

            return $result->returnValue;
        }

        return $result
            ->setRuntimeContext($this->context)
            ->setUserlandHeapSpace($this->userlandHeapSpace);
    }

    public function valueOf(): mixed
    {
        return null;
    }
}
