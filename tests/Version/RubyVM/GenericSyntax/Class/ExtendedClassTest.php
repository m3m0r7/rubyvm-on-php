<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax\Class;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ExtendedClassTest extends TestApplication
{
    public function testExtendedMethodIntoDefaultProvidingClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Array
              def foo(text)
                puts text
              end
            end

            array1 = Array.new([1, 2, 3])

            # call extended method
            array1.foo("Hello World!")

            # call basic method
            for i in array1 do
                puts i
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        Hello World!
        1
        2
        3

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testExtendedPlusOperatorIntoIntegerClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Integer
              def +(s)
                self - s
              end
            end

            puts 3 + 1
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        2

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testExtendedDoubleDefinitionPlusAndMinusOperatorIntoIntegerClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Integer
              def +(s)
                self - s
              end
              def -(s)
                self * s
              end
            end

            puts 10 + 10
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        100

        _, $rubyVMManager->stdOut->readAll());
    }
}
