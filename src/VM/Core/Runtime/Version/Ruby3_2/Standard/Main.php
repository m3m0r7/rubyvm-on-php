<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Version\Ruby3_2\Standard;

use RubyVM\VM\Core\Runtime\MainInterface;
use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

class Main implements MainInterface
{
    public function puts(StringSymbol $string): void
    {
        echo $string;
    }
}
