<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class BooleanTest extends TestApplication
{
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
}
