<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;

class UserlandHeapSpace implements UserlandHeapSpaceInterface
{
    public readonly UserlandClassEntries $userlandClasses;

    public readonly UserlandMethodEntries $userlandMethods;

    public readonly UserlandInstanceVariableEntries $userlandInstanceVariables;

    public function __construct()
    {
        $this->userlandClasses = new UserlandClassEntries();
        $this->userlandMethods = new UserlandMethodEntries();
        $this->userlandInstanceVariables = new UserlandInstanceVariableEntries();
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

    public function userlandInstanceVariables(): UserlandInstanceVariableEntries
    {
        return $this->userlandInstanceVariables;
    }
}
