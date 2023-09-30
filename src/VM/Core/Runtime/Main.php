<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Main implements MainInterface
{
    use ShouldBeRubyClass;

    protected ?Class_ $entity = null;

    public function entity(): Class_
    {
        return $this->entity ??= Class_::createBy(new StringSymbol('<main>'));
    }

    public function __toString(): string
    {
        return '<main>';
    }
}
