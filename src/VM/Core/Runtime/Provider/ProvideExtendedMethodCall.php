<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Symbol\Object_;
use RubyVM\VM\Exception\NotFoundInstanceMethod;

trait ProvideExtendedMethodCall
{
    use CallBlockHelper;

    public function __call(string $name, array $arguments): ExecutedResult|Object_
    {
        $context = $this->userlandHeapSpace?->userlandMethods()->get($name);

        if ($context === null) {
            throw new NotFoundInstanceMethod(sprintf('Method not found %s#%s', ClassHelper::nameBy($this), $name));
        }

        if (is_string($context)) {
            return $this->__call($context, $arguments);
        }

        return $this->callSimpleMethod(
            $context,
            ...$arguments,
        );
    }
}
