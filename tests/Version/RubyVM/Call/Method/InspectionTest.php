<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class InspectionTest extends TestApplication
{
    public function testCallInspectionOnArray(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts Array.new([5, 4, 3, 2, 1]).inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("[5, 4, 3, 2, 1]\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnString(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "Hello World!".inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("\"Hello World!\"\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnNumber(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1234.inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1234\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnFloat(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1.5.inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1.5\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnBoolean(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts true.inspect
            puts false.inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("true\nfalse\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnNil(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts nil.inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("nil\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCallInspectionOnRange(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1..5).inspect
            puts (1...5).inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1..5\n1...5\n", $rubyVMManager->stdOut->readAll());
    }
}
