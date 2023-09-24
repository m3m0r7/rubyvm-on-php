<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entity\Class_;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Main implements MainInterface
{
    use ShouldBeRubyClass;

    protected ?EntityInterface $entity = null;

    public function entity(): EntityInterface
    {
        return $this->entity ??= Class_::createBy(new StringSymbol('<main>'));
    }

    public function __toString(): string
    {
        return '<main>';
    }
}
