<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\RubyClass;

trait DebugFormat
{
    public function __toString(): string
    {
        $targetItems = $this->items ?? [];

        $result = [];
        foreach ($targetItems as $index => $item) {
            if ($item instanceof RubyClassInterface) {
                $result[] = ClassHelper::nameBy($item) . "({$item})";

                continue;
            }

            $result[] = match ($item::class) {
                Operand::class => match (($item->operand)::class) {
                    RubyClass::class => ClassHelper::nameBy($item->operand) . "({$item->operand})",
                    default => ClassHelper::nameBy($item->operand),
                },
                default => 'unknown',
            } . "#{$index}";
        }

        return rtrim(
            implode(', ', $result),
            ", \t\n\r\0\x0B",
        );
    }
}
