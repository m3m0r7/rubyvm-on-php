<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime;

use RubyVM\VM\Core\Runtime\Executor\ContextInterface;
use RubyVM\VM\Core\Runtime\Version\Ruby3_2\Entry\AliasEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\YARV\Criterion\Entry\EntryType;
use RubyVM\VM\Core\YARV\Criterion\UserlandHeapSpaceInterface;

final class UserlandClassEntries extends AbstractEntries
{
    protected AliasEntries $aliases;

    public function __construct()
    {
        parent::__construct();
        $this->aliases = new AliasEntries();
    }

    /**
     * When $value is a string, which is set an alias on PHP method/function
     * When $value is a ContextInterface, which is having native code
     * Otherwise, set deeply entry when $value is instantiated by UserlandHeapSpaceInterface.
     */
    public function verify(mixed $value): bool
    {
        return is_string($value) || $value instanceof ContextInterface || $value instanceof UserlandHeapSpaceInterface;
    }

    public function alias(string $name, string $to): self
    {
        $this->aliases->set($name, $to);

        return $this;
    }

    public function aliasNameBy(string $name): string|null
    {
        return $this->aliases->get($name);
    }

    public function has(mixed $index): bool
    {
        $result = parent::has($index);
        if ($result === true) {
            return true;
        }
        if ($this->aliases->has($index)) {
            $index = $this->aliases->has($index);
        }

        return parent::has($index);
    }

    public function get(mixed $index): mixed
    {
        $result = parent::get($index);
        if ($result !== null) {
            return $result;
        }
        if ($this->aliases->has($index)) {
            $index = $this->aliases->get($index);
        }

        return parent::get($index);
    }

    public function set(mixed $index, mixed $value): AbstractEntries
    {
        // Add normally
        parent::set($index, $value);

        // Add into aliased entry
        if ($this->aliases->has($index)) {
            $index = $this->aliases->get($index);
            parent::set($index, $value);
        }

        return $this;
    }

    protected function entryType(): EntryType
    {
        return EntryType::HASH;
    }
}
