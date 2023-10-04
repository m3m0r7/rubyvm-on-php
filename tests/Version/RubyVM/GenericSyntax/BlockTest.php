<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class BlockTest extends TestApplication
{
    public function testBlock(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test( &block )
              block.call
            end

            test do
              puts "Hello World!"
            end

            test do
              puts "2nd Hello World!"
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("Hello World!\n2nd Hello World!\n", $rubyVMManager->stdOut->readAll());
    }
}
