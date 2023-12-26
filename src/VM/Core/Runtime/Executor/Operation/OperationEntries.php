<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\UnknownEntry;

class OperationEntries extends AbstractEntries implements \Stringable
{
    public function verify(mixed $value): bool
    {
        return $value instanceof Operation
            || $value instanceof Operand
            || $value instanceof UnknownEntry;
    }

    public function __toString(): string
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = ClassHelper::nameBy($item) . '<' . match ($item::class) {
                Operation::class => sprintf(
                    '%s<0x%02x>',
                    strtolower((string) $item->insn->name),
                    $item->insn->value
                ),
                Operand::class => ClassHelper::nameBy($item->operand),
                default => 'none',
            } . '>';
        }

        return implode(', ', $result);
    }
}
