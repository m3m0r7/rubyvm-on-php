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
class ClassTest extends TestApplication
{
    public function testCallAutomaticallyAddedMethodsInClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Test
              def foo(text)
                puts text
              end
            end

            Test.new.foo("Hello World!")
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("Hello World!\n", $rubyVMManager->stdOut->readAll());
    }

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

    public function testInstanceVariable(): void
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
            ->disassemble(RubyVersion::VERSION_3_2);

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

    public function testStaticClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Cat
              class << self
                def name1
                  "ERU"
                end
                def name2
                  "GURI"
                end
              end
            end

            puts Cat.name1
            puts Cat.name2
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        ERU
        GURI

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testStaticClassItsImplementingDuplicatedSingletonClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Cat1
              class << self
                def name
                  "ERU"
                end
              end
            end

            class Cat2
              class << self
                def name
                  "GURI"
                end
              end
            end

            puts Cat1.name
            puts Cat2.name
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        ERU
        GURI

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testDynamicMethodItsCallingAsKeywordsArguments(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Animal
              def name(a:, b:, c:)
                puts a
                puts b
                puts c
              end
            end

            Animal.new.name(c: "neko", a: "tanuki", b: "inu")

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        tanuki
        inu
        neko

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testStaticMethodItsCallingAsKeywordsArguments(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Animal
              class << self
                  def name(a:, b:, c:)
                    puts a
                    puts b
                    puts c
                  end
              end
            end
            Animal.name(c: "neko", a: "tanuki", b: "inu")
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<< '_'
        tanuki
        inu
        neko

        _, $rubyVMManager->stdOut->readAll());
    }
}
