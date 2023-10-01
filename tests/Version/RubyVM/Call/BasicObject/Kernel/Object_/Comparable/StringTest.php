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
class StringTest extends TestApplication
{
    public function testEven(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "".empty?
            puts "Hello World!".empty?
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false

        _, $rubyVMManager->stdOut->readAll());
    }
}
