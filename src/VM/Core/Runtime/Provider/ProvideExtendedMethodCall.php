<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Symbol\SymbolInterface;
use RubyVM\VM\Exception\OperationProcessorException;

trait ProvideExtendedMethodCall
{
    use CallBlockHelper;

    public function __call(string $name, array $arguments): ExecutedResult|SymbolInterface
    {
        if ($this->extendedClassEntry && $this->extendedClassEntry->hasMethod($name)) {
            return $this->extendedClassEntry->{$name}(...$arguments);
        }

        /**
         * @var null|ContextInterface $context
         */
        $context = $this->kernel->userlandHeapSpace()->userlandMethods[$name] ?? null;

        if ($context === null) {
            throw new OperationProcessorException(sprintf('Method not found %s#%s', ClassHelper::nameBy($this), $name));
        }

        return $this->callSimpleMethod(
            $context,
            ...$arguments,
        );
    }
}
