<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\DefinedClassEntries;
use RubyVM\VM\Core\Runtime\Provider\ProvideBasicClassMethods;
use RubyVM\VM\Core\Runtime\Provider\ProvideClassExtendableMethods;
use RubyVM\VM\Core\Runtime\Provider\ProvideExtendedMethodCall;
use RubyVM\VM\Core\Runtime\Provider\ProvideInjectableVMContext;
use RubyVM\VM\Core\Runtime\Provider\ProvidePHPClassMethods;

trait ShouldBeRubyClass
{
    use ProvideBasicClassMethods;
    use ProvideClassExtendableMethods;
    use ProvideExtendedMethodCall;
    use ProvidePHPClassMethods;
    use ProvideInjectableVMContext;

    protected KernelInterface $kernel;
    protected DefinedClassEntries $definedClassEntries;
}
