<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion;

use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Provider\ProvideBasicClassMethods;
use RubyVM\VM\Core\Runtime\Provider\ProvideClassExtendableMethods;
use RubyVM\VM\Core\Runtime\Provider\ProvideExtendedMethodCall;
use RubyVM\VM\Core\Runtime\Provider\ProvidePHPClassMethods;
use RubyVM\VM\Exception\OperationProcessorException;

trait ShouldBeRubyClass
{
    use ProvideBasicClassMethods;
    use ProvideClassExtendableMethods;
    use ProvideExtendedMethodCall;
    use ProvidePHPClassMethods;

    protected ?ContextInterface $context = null;

    public function setRuntimeContext(?ContextInterface $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function context(): ContextInterface
    {
        // @phpstan-ignore-next-line
        if ($this->context === null) {
            throw new OperationProcessorException('The runtime context is not injected - did you forget to call setRuntimeContext before?');
        }
        return $this->context;
    }
}
