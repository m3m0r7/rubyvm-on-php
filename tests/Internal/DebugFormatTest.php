<?php

declare(strict_types=1);

namespace Tests\RubyVM\Internal;

use RubyVM\VM\Core\Criterion\Entry\AbstractEntries;
use RubyVM\VM\Core\Runtime\BasicObject\Kernel\Object_\Comparable\Integer_;
use RubyVM\VM\Core\Runtime\Executor\Debugger\DebugFormat;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operand;
use RubyVM\VM\Core\Runtime\Executor\Operation\Operation;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_2\InstructionSequence\Insn as Ruby3_2_Insn;
use RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\InstructionSequence\Insn as Ruby3_3_Insn;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class DebugFormatTest extends TestApplication
{
    public function testDebugFormat(): void
    {
        $class = new class () extends AbstractEntries {
            use DebugFormat;

            public function __toString(): string
            {
                return self::getEntriesAsString(
                    $this->items ?? [],
                );
            }
        };

        $class[] = new Operation(match ("{$this->major}.{$this->minor}") {
            "3.3" => Ruby3_3_Insn::SEND,
            default => Ruby3_2_Insn::SEND,
        });
        $class[] = new Operand(Integer_::createBy(0));
        $class[] = new Operand(Integer_::createBy(65535));
        $class[] = new Operand(Integer_::createBy(-128));

        $this->assertSame('Operation(SEND)#0, Integer(0)#1, Integer(65535)#2, Integer(-128)#3', (string) $class);
    }
}
