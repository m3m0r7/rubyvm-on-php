<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax\Class;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class InstanceVariableTest extends TestApplication
{
    public function testInstanceVariableForNonExistsClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Test
              def foo(arr1, arr2)
                @arr1 = arr1
                @arr2 = arr2
                self
              end
              def test
                puts @arr1
                puts @arr2
              end
            end

            Test.new.foo([5, 4, 3, 2, 1], [10, 100, 1000]).test
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        5
        4
        3
        2
        1
        10
        100
        1000

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testInstanceVariableForAlreadyExistsClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Array
              def foo(arr1, arr2)
                @arr1 = arr1
                @arr2 = arr2
                self
              end
              def test
                puts @arr1
                puts @arr2
              end
            end

            Array.new.foo([5, 4, 3, 2, 1], [10, 100, 1000]).test
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        5
        4
        3
        2
        1
        10
        100
        1000

        _, $rubyVMManager->stdOut->readAll());
    }
}
