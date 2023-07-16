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

    public function testNumberMultiplyNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 2 * 4 * 8
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("64", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberDivideNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 4 / 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("1", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatPlusFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 1.0 + 2.0 + 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("6.0", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatMinusFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 1.0 - 2.0 - 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("-4.0", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatMultiplyFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 2.0 * 4.0 * 8.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("64.0", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatDivideFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 4.0 / 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("1.3333333333333333", $rubyVMManager->stdOut->readAll());
    }

    public function testLocalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            variable_test1 = 15
            variable_test2 = 10
            puts variable_test1 + variable_test2
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("25", $rubyVMManager->stdOut->readAll());
    }

    public function testMod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 3 % 5
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("3", $rubyVMManager->stdOut->readAll());
    }

    public function testAnd(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 3 & 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("1", $rubyVMManager->stdOut->readAll());
    }

    public function testOr(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 2 | 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("3", $rubyVMManager->stdOut->readAll());
    }

    public function testLeftShift(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts 2 << 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("4", $rubyVMManager->stdOut->readAll());
    }
    public function testTrueAndFalse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts true && false
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("false", $rubyVMManager->stdOut->readAll());
    }
    public function testTrueOrFalse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts true || false
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("true", $rubyVMManager->stdOut->readAll());
    }
    public function testTrueAndTrue(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts true && true
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("true", $rubyVMManager->stdOut->readAll());
    }

    public function testMultiBoolean(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            boolean1 = true
            boolean2 = false
            boolean3 = true
            puts boolean1 && boolean2 || boolean3 && boolean1 || boolean2 && boolean3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("true", $rubyVMManager->stdOut->readAll());
    }
}
