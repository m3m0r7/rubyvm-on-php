<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class LambdaTest extends TestApplication
{
    public function testProc(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p = ->(word) { puts word }
            p.call("Hello World!")
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("Hello World!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testComplexProc(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p = ->(hello, word) {
              e = "!"
              puts hello + " " + word + e
            }
            p.call("Hello", "World")

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("Hello World!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testNonArgumentsProc(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p = -> {
              puts "Hello World!"
            }
            p.call

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("Hello World!\n", $rubyVMManager->stdOut->readAll());
    }
}
