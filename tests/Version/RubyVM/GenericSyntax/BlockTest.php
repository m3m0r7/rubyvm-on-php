<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class BlockTest extends TestApplication
{
    public function testBlock(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test(&block)
              block.call
            end

            test do
              puts "1st Hello World!"
            end

            test do
              puts "2nd Hello World!"
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1st Hello World!\n2nd Hello World!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testBlockWithVariables(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test(&block)
              block.call("Hello", "World!")
            end

            test do | var1, var2 |
              puts "1st " + var1 + " " + var2
            end

            test do | var1, var2 |
              puts "2nd " + var1 + " " + var2
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1st Hello World!\n2nd Hello World!\n", $rubyVMManager->stdOut->readAll());
    }

    public function testBlockWithVariablesWithOutsideVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test(&block)
              block.call("Hello", "World!")
            end

            var3 = "with outside"

            test do | var1, var2 |
              puts "1st " + var1 + " " + var2 + " " + var3
            end

            test do | var1, var2 |
              puts "2nd " + var1 + " " + var2 + " " + var3
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1st Hello World! with outside\n2nd Hello World! with outside\n", $rubyVMManager->stdOut->readAll());
    }

    public function testBlockComplexVariables(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def test(&block)
              block.call("Hello", "World!")
            end

            def callee(var4)
                var3 = "with outside"
                test do | var1, var2 |
                  puts "1st " + var1 + " " + var2 + " " + var3 + " " + var4
                end

                test do | var1, var2 |
                  puts "2nd " + var1 + " " + var2 + " " + var3 + " " + var4
                end
            end

            callee("! :)")

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("1st Hello World! with outside ! :)\n2nd Hello World! with outside ! :)\n", $rubyVMManager->stdOut->readAll());
    }
}
