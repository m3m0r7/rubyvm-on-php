<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Executor\Debugger\DebugFormat;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Exception\LocalTableException;

class EnvironmentTable extends AbstractEntries
{
    use DebugFormat;

    /**
     * @var array<string, bool>
     */
    protected array $leads = [];

    public function verify(mixed $value): bool
    {
        return $value instanceof RubyClass;
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
        // do not set new value if have a lead flag.
        if (isset($this->leads[$index]) && $this->leads[$index] === true) {
            // Forcibly set to non lead
            $this->leads[$index] = false;

            return $this;
        }

        return parent::set($index, $value);
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
}
