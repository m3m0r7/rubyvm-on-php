<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

interface UserlandHeapSpaceInterface
{
    public function userlandClasses(): UserlandClassEntries;

    public function userlandMethods(): UserlandMethodEntries;
}
