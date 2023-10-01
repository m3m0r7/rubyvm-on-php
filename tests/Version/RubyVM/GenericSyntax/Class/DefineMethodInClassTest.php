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
class DefineMethodInClassTest extends TestApplication
{
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
