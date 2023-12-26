<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ConcatTest extends TestApplication
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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("HelloWorld!\n", $rubyVMManager->stdOut->readAll());
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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("HelloWorld65535\n", $rubyVMManager->stdOut->readAll());
    }

    public function testConcatArray(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            v = [4, 5, 6]
            puts [*[1, 2, 3], *v].inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("[1, 2, 3, 4, 5, 6]\n", $rubyVMManager->stdOut->readAll());
    }

    public function testSplatArrayWithVariables(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            var1 = [1, 2, 3]
            var2 = [4, 5, 6]

            puts [*var1, *var2].inspect
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        [1, 2, 3, 4, 5, 6]

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testSplatArrayInMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def arr(args)
              puts [*args, *[4, 5, 6]].inspect
            end

            arr 1
            arr [1, 2, 3]
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        [1, 4, 5, 6]
        [1, 2, 3, 4, 5, 6]

        _, $rubyVMManager->stdOut->readAll());
    }
}
