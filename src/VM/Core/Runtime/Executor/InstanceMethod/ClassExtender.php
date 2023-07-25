<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\InstanceMethod;

use RubyVM\VM\Exception\ClassNotFoundException;

class ClassExtender
{
    public function __construct(public readonly ExtendedClassEntries $extendedClasses = new ExtendedClassEntries())
    {
    }

    public function extend(string $name, string $bindClass): self
    {
        $this->extendedClasses->set($name, new MethodExtender($this, $name, $bindClass));
        return $this;
    }

    public function is(string $class): bool
    {
        foreach ($this->extendedClasses as $name => $entry) {
            if ($class === $name) {
                return true;
            }
        }
        return false;
    }

    public function hasByBindClass(string $class): bool
    {
        var_dump($class, $this->extendedClasses);
        foreach ($this->extendedClasses as $entry) {
            if ($class === $entry) {
                return true;
            }
        }
        return false;
    }

    public function get(string $name): MethodExtender
    {
        return $this->extendedClasses->get($name) ?? throw new ClassNotFoundException(
            sprintf(
                'The class not defined (%s)',
                $name,
            ),
        );
    }
}
