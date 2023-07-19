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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('HelloWorld!', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('6', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('-4', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('64', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('1', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('6.0', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('-4.0', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('64.0', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('1.3333333333333333', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('25', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('3', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('1', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('3', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('4', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('false', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true', $rubyVMManager->stdOut->readAll());
    }
    public function testStringPlusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts "HelloWorld" + 65535.to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('HelloWorld65535', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true', $rubyVMManager->stdOut->readAll());
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

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true', $rubyVMManager->stdOut->readAll());
    }
    public function testManyLocalVariables(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            boolean1 = true
            boolean2 = false
            boolean3 = true
            boolean4 = true
            boolean5 = true
            boolean6 = true
            boolean7 = true
            boolean8 = true
            boolean9 = true
            boolean10 = true
            boolean11 = true
            boolean12 = true
            boolean13 = true
            boolean14 = true
            boolean15 = true
            boolean16 = true
            puts boolean1 || boolean2 || boolean3 || boolean4 || boolean5 || boolean6 || boolean7 || boolean8 || boolean9 || boolean10 || boolean11 || boolean12 || boolean13 || boolean14 || boolean15 || boolean16
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true', $rubyVMManager->stdOut->readAll());
    }


    public function testDefineMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            def test(i)
                puts i.to_s
            end
            test(65535)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('65535', $rubyVMManager->stdOut->readAll());
    }

    public function testCompareLessThan()
    {

        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts (1 < 20).to_s
            puts ","
            puts (100 < 20).to_s
            puts ","
            puts (20 < 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true,false,false', $rubyVMManager->stdOut->readAll());
    }

    public function testCompareLessOrEqualsThan()
    {

        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts (1 <= 20).to_s
            puts ","
            puts (100 <= 20).to_s
            puts ","
            puts (20 <= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('true,false,true', $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterThan()
    {

        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts (1 > 20).to_s
            puts ","
            puts (100 > 20).to_s
            puts ","
            puts (20 > 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('false,true,false', $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterOrEqualsThan()
    {

        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts (1 >= 20).to_s
            puts ","
            puts (100 >= 20).to_s
            puts ","
            puts (20 >= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame('false,true,true', $rubyVMManager->stdOut->readAll());
    }
}
