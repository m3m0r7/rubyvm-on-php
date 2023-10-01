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
class DefineStaticMethodTest extends TestApplication
{
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
}
