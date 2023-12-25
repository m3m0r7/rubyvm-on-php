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
class ArithmeticTest extends TestApplication
{
    public function testNumberPlusNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1 + 2 + 3
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1.3333333333333333\n", $rubyVMManager->stdOut->readAll());
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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("4\n", $rubyVMManager->stdOut->readAll());
    }
}
