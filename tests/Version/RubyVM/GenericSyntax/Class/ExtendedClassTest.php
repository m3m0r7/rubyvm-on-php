<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\GenericSyntax\Class;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
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
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        Hello World!
        1
        2
        3

        _, $rubyVMManager->stdOut->readAll());
    }
}
