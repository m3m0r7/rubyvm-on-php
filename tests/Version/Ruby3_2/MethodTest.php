<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class MethodTest extends TestApplication
{
    public function testPutsMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "HelloWorld!"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("HelloWorld!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testPHPInfoMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            phpinfo
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $version = PHP_VERSION;
        $this->assertStringStartsWith(<<<_
        phpinfo()
        PHP Version => {$version}

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testXOR(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2^5
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("7\n", $rubyVMManager->stdOut->readAll());
    }

    public function testPower(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2**5
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("32\n", $rubyVMManager->stdOut->readAll());
    }

    public function testRightShift(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2>>1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1\n", $rubyVMManager->stdOut->readAll());
    }

    public function testToInt(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 65535.to_i
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
    }

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

    public function testVariadicArguments(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def variadic_arguments(a, b, *params)
              puts a
              params.each do |param|
                puts param
              end

              # Check if VM stack is not empty (call a method testing)
              puts b
            end
            variadic_arguments(1, 5, 2, 3, 4)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1\n2\n3\n4\n5\n", $rubyVMManager->stdOut->readAll());
    }
}
