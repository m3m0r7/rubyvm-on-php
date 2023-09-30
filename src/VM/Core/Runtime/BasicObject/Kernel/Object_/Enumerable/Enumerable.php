<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Object_;

abstract class Enumerable extends Object_ implements \ArrayAccess, \IteratorAggregate, \Countable {}
