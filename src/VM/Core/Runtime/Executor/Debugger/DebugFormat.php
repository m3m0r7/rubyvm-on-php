<?php

declare(strict_types=1);

namespace RubyVM\VM\Core\Runtime\Executor\Debugger;

use RubyVM\VM\Core\Helper\ClassHelper;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\FalseClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\NilClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\TrueClass;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Undefined;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Void_;
use RubyVM\VM\Core\Runtime\Essential\RubyClassInterface;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;

trait DebugFormat
{
    public function __toString(): string
    {
        $targetItems = $this->items ?? [];

        $result = [];
        foreach ($targetItems as $index => $item) {
            if ($item instanceof RubyClassInterface) {
                $result[] = ClassHelper::nameBy($item) . "({$item})". "#{$index}";

                continue;
            }

            if ($item instanceof Operand) {
                if ($item->operand instanceof RubyClassInterface) {
                    $result[] = match ($item->operand::class) {
                        TrueClass::class, FalseClass::class, NilClass::class, Void_::class, Undefined::class => ClassHelper::nameBy($item->operand),
                        default => ClassHelper::nameBy($item->operand) . "({$item->operand})",
                        } . "#{$index}";

                    continue;
                }

                $result[] = ClassHelper::nameBy($item->operand) . "#{$index}";

                continue;
            }
        }

        return rtrim(
            implode(', ', $result),
            ", \t\n\r\0\x0B",
        );
    }
}
