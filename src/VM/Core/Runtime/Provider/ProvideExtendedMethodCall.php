<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Provider;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\CallBlockHelper;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\ExecutedResult;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Criterion\InstructionSequence\CallInfoInterface;
use RubyVM\VM\Exception\NotFoundInstanceMethod;
use RubyVM\VM\Exception\OperationProcessorException;
use RubyVM\VM\Exception\RuntimeException;

trait ProvideExtendedMethodCall
{
    use CallBlockHelper;

    /**
     * @param (ContextInterface|RubyClassInterface)[] $arguments
     */
    public function __call(string $name, array $arguments): ExecutedResult|RubyClassInterface|null
    {
        // @phpstan-ignore-next-line
        if ($this->context === null) {
            throw new OperationProcessorException('The runtime context is not injected - did you forget to call setRuntimeContext before?');
        }

        $context = $this->userlandHeapSpace()->userlandMethods()->get($name);

        if ($context === null) {
            $boundClass = $this
                ->context
                ->self()
                ->userlandHeapSpace()
                ->userlandClasses()
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

        if (($arguments[0] ?? null) !== null && !$arguments[0] instanceof CallInfoInterface) {
            throw new RuntimeException('A CallInfo entry was not passed');
        }

        return $this
            ->callSimpleMethod(
                $context,

                // @phpstan-ignore-next-line
                $arguments[0],
                ...array_slice($arguments, 1),
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
