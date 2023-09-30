<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_;

use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Kernel;
use RubyVM\VM\Core\Runtime\Entity\Entityable;
use RubyVM\VM\Core\Runtime\Entity\EntityInterface;

abstract class Object_ extends Kernel implements EntityInterface
{
    use Entityable;
}
