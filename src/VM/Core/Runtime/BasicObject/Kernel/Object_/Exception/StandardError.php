<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Exception;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;

abstract class StandardError extends Exception implements RubyClassInterface {}
