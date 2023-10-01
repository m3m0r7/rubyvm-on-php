<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\Call\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ArrayTest extends TestApplication
{
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

    public function testForStatementWithLocalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            test_array = [1, 2, 3, 4, 5]

            for i in test_array
              puts i.to_s + "\n"
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

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testForStatementWithLocalVariableAnotherSyntax(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            [1, 2, 3, 4, 5].each do | i |
              puts i.to_s + "\n"
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

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testShowArrayInMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def display_array(arr)
                puts arr
            end

            puts display_array([1, 2, 3])
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


        _, $rubyVMManager->stdOut->readAll());
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def display_array(arr)
                arr
            end

            puts display_array([1, 2, 3])
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

        _, $rubyVMManager->stdOut->readAll());
    }
}
