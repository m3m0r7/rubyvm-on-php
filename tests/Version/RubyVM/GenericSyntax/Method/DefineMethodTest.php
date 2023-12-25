<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class DefineMethodTest extends TestApplication
{
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
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
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
            ->disassemble();

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
            ->disassemble();

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
            ->disassemble();

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
}
