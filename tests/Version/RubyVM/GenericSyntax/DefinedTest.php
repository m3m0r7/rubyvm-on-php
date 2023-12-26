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
class DefinedTest extends TestApplication
{
    public function testDefinedVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            var = 1234
            p defined?(var)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("\"local-variable\"\n", $rubyVMManager->stdOut->readAll());
    }

    public function testDefinedGlobalVariable(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            $var = 1234
            p defined?($var)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("\"global-variable\"\n", $rubyVMManager->stdOut->readAll());
    }

    public function testDefinedMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def var
            end
            p defined?(var)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("\"method\"\n", $rubyVMManager->stdOut->readAll());
    }

    public function testDefinedClass(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            class Var
            end
            p defined?(Var)
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("\"constant\"\n", $rubyVMManager->stdOut->readAll());
    }
}
