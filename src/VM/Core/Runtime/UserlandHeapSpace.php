<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

class UserlandHeapSpace implements UserlandHeapSpaceInterface
{
    public readonly UserlandClassEntries $userlandClasses;
    public readonly UserlandMethodEntries $userlandMethods;

    public function __construct()
    {
        $this->userlandClasses = new UserlandClassEntries();
        $this->userlandMethods = new UserlandMethodEntries();
    }

    public function __debugInfo(): ?array
    {
        return [];
    }

    public function userlandClasses(): UserlandClassEntries
    {
        return $this->userlandClasses;
    }

    public function userlandMethods(): UserlandMethodEntries
    {
        return $this->userlandMethods;
    }
}
