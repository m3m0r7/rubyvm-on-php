<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;

class Main implements MainInterface
{
    use ShouldBeRubyClass;
}
