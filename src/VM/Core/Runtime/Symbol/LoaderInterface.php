<?php
declare(strict_types=1);
namespace RubyVM\VM\Core\Runtime\Symbol;

use RubyVM\VM\Core\Runtime\Offset\Offset;

interface LoaderInterface
{
    public function load();
}
