<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor;

use RubyVM\VM\Core\Runtime\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\Executor\Insn\Insn;
use RubyVM\VM\Exception\OperationProcessorException;

class OperationProcessorEntries extends AbstractEntries
{
    public function verify(mixed $value): bool
    {
        return $value instanceof OperationProcessorInterface;
    }

    public function verifyOffset(mixed $key): bool
    {
        return $key instanceof Insn;
    }

    public function get(mixed $index): OperationProcessorInterface
    {
        if (!$index instanceof Insn) {
            throw new OperationProcessorException(sprintf('The passed index is not instantiated by an Insn enum - you should to specify parameter with instantiated by Insn enum'));
        }

        $processor = parent::get($index);
        if (!$processor instanceof OperationProcessorInterface) {
            throw new OperationProcessorException(sprintf('The `%s` (opcode: 0x%02x) processor is not registered', strtolower($index->name), $index->value));
        }

        return $processor;
    }
}
