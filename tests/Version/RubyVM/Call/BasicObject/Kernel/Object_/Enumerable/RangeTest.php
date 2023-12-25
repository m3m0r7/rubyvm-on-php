<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class RangeTest extends TestApplication
{
    public function testCallEachBlockNonExcludedAndExcluded(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            (1..5).each do | i |
                puts i
            end

            (1...5).each do | i |
                puts i
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        1
        2
        3
        4
        5
        1
        2
        3
        4

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testInfinityRange(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1.. === Float::INFINITY
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true

        _, $rubyVMManager->stdOut->readAll());
    }
}
