<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

interface RubyClassExtendableInterface extends RubyClassInterface
{
    public function extendClassEntry(ExtendedClassEntry $extendedClassEntry): self;
}
