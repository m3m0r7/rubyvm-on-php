<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Entity\Entityable;
use RubyVM\VM\Core\Runtime\Essential\MainInterface;
use RubyVM\VM\Core\YARV\Criterion\ShouldBeRubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\StringSymbol;

class Main implements MainInterface
{
    use ShouldBeRubyClass;
    use Entityable;

    public function __construct()
    {
        $this->symbol = new StringSymbol('<main>');
    }

    public function __toString(): string
    {
        return '<main>';
    }
}
