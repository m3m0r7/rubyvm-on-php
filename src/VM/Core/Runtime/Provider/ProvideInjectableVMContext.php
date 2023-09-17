<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\KernelInterface;

trait ProvideInjectableVMContext
{
    public function injectVMContext(
        KernelInterface $kernel,
        DefinedClassEntries $definedClassEntries = null,
    ): self {
        $this->kernel = $kernel;
        $this->definedClassEntries = $definedClassEntries ?? new DefinedClassEntries();

        foreach ($this->definedClassEntries as $className => $definedClassEntry) {
            static::$userLandClasses[$className] = $definedClassEntry;
        }

        return $this;
    }
}
