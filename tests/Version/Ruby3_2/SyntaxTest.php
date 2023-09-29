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
class SyntaxTest extends TestApplication
{
    public function testConcatString(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "Hello" + "World" + "!"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("HelloWorld!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberPlusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1 + 2 + 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("6\n", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberMinusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1 - 2 - 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("-4\n", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberMultiplyNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2 * 4 * 8
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("64\n", $rubyVMManager->stdOut->readAll());
    }

    public function testNumberDivideNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 4 / 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1\n", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatPlusFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1.0 + 2.0 + 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("6.0\n", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatMinusFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1.0 - 2.0 - 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("-4.0\n", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatMultiplyFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2.0 * 4.0 * 8.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("64.0\n", $rubyVMManager->stdOut->readAll());
    }

    public function testFloatDivideFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 4.0 / 3.0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1.3333333333333333\n", $rubyVMManager->stdOut->readAll());
    }

    public function testLocalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            variable_test1 = 15
            variable_test2 = 10
            puts variable_test1 + variable_test2
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("25\n", $rubyVMManager->stdOut->readAll());
    }

    public function testMod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 3 % 5
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("3\n", $rubyVMManager->stdOut->readAll());
    }

    public function testAnd(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 3 & 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1\n", $rubyVMManager->stdOut->readAll());
    }

    public function testOr(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2 | 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("3\n", $rubyVMManager->stdOut->readAll());
    }

    public function testLeftShift(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 2 << 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("4\n", $rubyVMManager->stdOut->readAll());
    }

    public function testTrueAndFalse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts true && false
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("false\n", $rubyVMManager->stdOut->readAll());
    }

    public function testTrueOrFalse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts true || false
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("true\n", $rubyVMManager->stdOut->readAll());
    }

    public function testStringPlusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "HelloWorld" + 65535.to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("HelloWorld65535\n", $rubyVMManager->stdOut->readAll());
    }

    public function testTrueAndTrue(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts true && true
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("true\n", $rubyVMManager->stdOut->readAll());
    }

    public function testMultiBoolean(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
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
        $this->assertSame("true\n", $rubyVMManager->stdOut->readAll());
    }

    public function testManyLocalVariables(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
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
        $this->assertSame("true\n", $rubyVMManager->stdOut->readAll());
    }

    public function testDefineMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
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
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCompareLessThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 < 20).to_s
            puts (100 < 20).to_s
            puts (20 < 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false
        false

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareLessOrEqualsThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 <= 20).to_s
            puts (100 <= 20).to_s
            puts (20 <= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false
        true

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 > 20).to_s
            puts (100 > 20).to_s
            puts (20 > 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true
        false

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterOrEqualsThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 >= 20).to_s
            puts (100 >= 20).to_s
            puts (20 >= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true
        true

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testNegativeValue(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts -32
            puts -16
            puts -8
            puts -7
            puts -3
            puts -2
            puts -1
            puts 0
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        -32
        -16
        -8
        -7
        -3
        -2
        -1
        0

        _, $rubyVMManager->stdOut->readAll());
    }

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

    public function testLocalVarAllOptionalAndAssignedParameters(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def local_optional_var_test(a = 1, b = 2, c = 3, d = 4)
                puts a
                puts b
                puts c
                puts d
            end

            local_optional_var_test(1111, 2222, 3333, 4444)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        1111
        2222
        3333
        4444

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testLocalVarAllOptionalAndOmittedParameters(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def local_optional_var_test(a = 1, b = 2, c = 3, d = 4)
                puts a
                puts b
                puts c
                puts d
            end

            local_optional_var_test()
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

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testComplexLocalVarPattern(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def local_optional_var_test(x, y, z, a = 1, b = 2, c = 3, d = 4, *variadic)
              e = 5
              puts x
              puts y
              puts z
              puts a
              puts b
              puts c
              puts d
              puts e
              variadic.each do | var |
                puts var
              end
            end
            local_optional_var_test(1111, 2222, 3333, 4444, 5555, 3, 4, 6666, 7777, 8888, 9999)

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        1111
        2222
        3333
        4444
        5555
        3
        4
        5
        6666
        7777
        8888
        9999

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testSymbol(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def symbol_test(a)
              puts a
            end

            symbol_test(:HelloWorld)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        HelloWorld

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testStringArray(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            %w(foo bar baz).each do | str |
              puts str
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        foo
        bar
        baz

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testHash(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            obj = { a: :symbol, b: 1234, c: 'Hello World!' }

            puts obj[:a]
            puts obj[:b]
            puts obj[:c]

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        symbol
        1234
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testRegExpWithMatched(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /is Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        matched!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testRegExpWithUnmatched(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /was Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        unmatched!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testContinuingRegExp(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /is Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end

            if /was Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        matched!
        unmatched!

        _, $rubyVMManager->stdOut->readAll());
    }
}
