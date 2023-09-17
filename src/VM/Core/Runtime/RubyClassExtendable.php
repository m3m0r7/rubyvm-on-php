<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

trait RubyClassExtendable
{
    use ShouldBeRubyClass;

    protected ?ExtendedClassEntry $extendedClassEntry = null;

    public function extendClassEntry(ExtendedClassEntry $extendedClassEntry): self
    {
        $this->extendedClassEntry = $extendedClassEntry;

        return $this;
    }
}
