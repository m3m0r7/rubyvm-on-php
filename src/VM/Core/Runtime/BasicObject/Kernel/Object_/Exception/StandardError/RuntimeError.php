<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception\StandardError;

use RubyVM\VM\Core\Runtime\Attribute\BindAliasAs;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception\StandardError;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;

#[BindAliasAs('RuntimeError')]
class RuntimeError extends StandardError implements RubyClassInterface {}
