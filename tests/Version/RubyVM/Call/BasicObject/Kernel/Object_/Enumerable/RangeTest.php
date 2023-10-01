<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\Call\BasicObject\Kernel\Object_\Enumerable;

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
            ->disassemble(RubyVersion::VERSION_3_2);

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
}
