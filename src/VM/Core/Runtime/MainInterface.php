<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Symbol\StringSymbol;

interface MainInterface
{
    public function puts(StringSymbol $string): void;
}
