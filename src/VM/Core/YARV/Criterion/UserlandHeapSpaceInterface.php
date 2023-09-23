<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\YARV\Criterion;

use RubyVM\VM\Core\Runtime\UserlandClassEntries;
use RubyVM\VM\Core\Runtime\UserlandMethodEntries;

interface UserlandHeapSpaceInterface
{
    public function userlandClasses(): UserlandClassEntries;

    public function userlandMethods(): UserlandMethodEntries;
}
