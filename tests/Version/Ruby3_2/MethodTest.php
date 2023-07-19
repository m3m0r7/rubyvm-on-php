<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

class MethodTest extends TestApplication
{
    public function testPutsMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
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
            <<< _
            phpinfo
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('PHP Version: ' . PHP_VERSION . "\n", $rubyVMManager->stdOut->readAll());
    }

    public function testXOR(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
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
            <<< _
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
            <<< _
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
            <<< _
            puts 65535.to_int
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
    }
}
