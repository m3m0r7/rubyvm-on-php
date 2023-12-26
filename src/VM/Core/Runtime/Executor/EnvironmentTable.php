<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Context\ContextInterface;
use RubyVM\VM\Core\Runtime\Executor\Debugger\DebugFormat;
use RubyVM\VM\Exception\LocalTableException;

class EnvironmentTable extends AbstractEntries implements \Stringable
{
    use DebugFormat;

    /**
     * @var array<int, bool>
     */
    protected array $leads = [];

    /**
     * @var array<int, string>
     */
    protected array $boundNames = [];

    public function verify(mixed $value): bool
    {
        return $value instanceof RubyClassInterface || $value instanceof ContextInterface;
    }

    public function get(mixed $index): mixed
    {
        if (!$this->has($index)) {
            throw new LocalTableException(
                sprintf(
                    'Failed to get from the LocalTable#%d because specified index is out of bound in the local table entries',
                    $index,
                ),
            );
        }

        return parent::get($index);
    }

    public function set(mixed $index, mixed $value): self
    {
        $index = (int) $index;
        // do not set new value if have a lead flag.
        if (isset($this->leads[$index]) && $this->leads[$index]) {
            // Forcibly set to non lead
            $this->leads[$index] = false;

            return $this;
        }

        parent::set($index, $value);

        return $this;
    }

    public function bindName(int $index, string $name): self
    {
        $this->boundNames[$index] = $name;

        return $this;
    }

    public function setWithLead(mixed $index, mixed $value, bool $isLead = false): self
    {
        return $this
            // Forcibly unset lead
            ->setLead($index, false)
            ->set($index, $value)
            // set renewed value
            ->setLead($index, $isLead);
    }

    public function lead(int $index): bool
    {
        return $this->leads[$index] ?? false;
    }

    public function setLead(int $index, bool $which): self
    {
        $this->leads[$index] = $which;

        return $this;
    }

    public function __toString(): string
    {
        return self::getEntriesAsString(
            $this->items ?? [],
        );
    }

    public function findBy(string $varName): null|RubyClassInterface
    {
        $pos = array_search($varName, $this->boundNames, true);
        if ($pos === false) {
            return null;
        }

        return $this->has($pos)
            ? $this[$pos]
            : null;
    }
}
