<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Symbolizable;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;
use RubyVM\VM\Exception\OperationProcessorException;

class Main implements MainInterface
{
    use ShouldBeRubyClass;
    use Symbolizable;

    public function __construct()
    {
        $this->symbol = new StringSymbol('<main>');
    }

    public function __toString(): string
    {
        return '<main>';
    }

    public function testValue(): bool
    {
        throw new OperationProcessorException(sprintf('The symbol type `%s` is not implemented `test` processing yet', ClassHelper::nameBy($this)));
    }
}
