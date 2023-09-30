<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Operation;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Executor\UnknownEntry;
use RubyVM\VM\Core\Runtime\RubyClass;
use RubyVM\VM\Core\YARV\Essential\Symbol\SymbolInterface;

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
                    strtolower($item->insn->name),
                    $item->insn->value
                ),
                Operand::class => match (($item->operand)::class) {
                    RubyClass::class => (string) $item->operand->symbol(),
                    SymbolInterface::class => (string) $item->operand,
                    default => ClassHelper::nameBy($item->operand),
                },
                default => 'none',
            } . '>';
        }

        return implode(', ', $result);
    }
}
