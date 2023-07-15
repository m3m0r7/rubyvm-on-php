<?php
declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

class SyntaxTest extends TestApplication
{
    public function testConcatString(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts "Hello" + "World" + "!"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("HelloWorld!", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberPlusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 1 + 2 + 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("6", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberMinusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 1 - 2 - 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("-4", $rubyVMManager->stdOut->readAll());
    }
}
