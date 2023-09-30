<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception\StandardError;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception\StandardError;
use RubyVM\VM\Core\Runtime\Essential\RubyClassifiable;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;

#[BindAliasAs('SystemCallError')]
class SystemCallError extends StandardError implements RubyClassInterface, RubyClassifiable {

    #[BindAliasAs('to_s')]
    public function __toString(): string
    {
        return 'unknown error - ' . parent::__toString();
    }
}
