<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\Object_;
use RubyVM\VM\Core\YARV\Essential\RubyClassInterface;
use RubyVM\VM\Exception\NotFoundInstanceMethod;

trait ProvideExtendedMethodCall
{
    use CallBlockHelper;

    public function __call(string $name, array $arguments): ExecutedResult|Object_
    {
        $context = $this->userlandHeapSpace?->userlandMethods()->get($name);

        if ($context === null) {
            $boundClass = $this
                ->context
                ->self()
                ->userlandHeapSpace()
                ->userlandClasses()
                ->get(static::resolveObjectName($this));

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
        if ($class instanceof Object_) {
            return ($class->symbol)::class;
        }

        return $class::class;
    }
}
