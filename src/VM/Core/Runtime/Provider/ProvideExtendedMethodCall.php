<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Exception\NotFoundInstanceMethod;
use RubyVM\VM\Exception\OperationProcessorException;

trait ProvideExtendedMethodCall
{
    use CallBlockHelper;

    /**
     * @param (ContextInterface|RubyClassInterface)[] $arguments
     */
    public function __call(string $name, array $arguments): ExecutedResult|RubyClassInterface|null
    {
        $context = $this->userlandHeapSpace?->userlandMethods()->get($name);

        if ($context === null) {
            if ($this->context === null) {
                throw new OperationProcessorException('The runtime context is not injected - did you forget to call setRuntimeContext before?');
            }

            $boundClass = $this
                ->context
                ->self()
                ->userlandHeapSpace()
                ?->userlandClasses()
                ->get(self::resolveObjectName($this));

            if ($boundClass !== null) {
                $context = $boundClass
                    ->userlandMethods()
                    ->get($name);
            }
        }

        if ($context === null) {
            throw new NotFoundInstanceMethod(sprintf('Method not found %s#%s', ClassHelper::nameBy($this), $name));
        }

        if (is_string($context)) {
            return $this->__call($context, $arguments);
        }

        return $this
            ->callSimpleMethod(
                $context,
                ...$arguments,
            );
    }

    private static function resolveObjectName(RubyClassInterface $class): string
    {
        if ($class instanceof RubyClass) {
            return ($class->entity)::class;
        }

        return $class::class;
    }
}
