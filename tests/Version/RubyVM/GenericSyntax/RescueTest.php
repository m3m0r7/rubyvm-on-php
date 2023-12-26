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
class RescueTest extends TestApplication
{
    public function testSimpleCatch(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            begin
              raise RuntimeError, 'Hello World! I am calling via raise expression'
            rescue RuntimeError => e
              puts e
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World! I am calling via raise expression

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testNestedCatch(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            begin
              raise RuntimeError, 'Hello World! I am calling via raise expression'
            rescue RuntimeError => e
              begin
                raise SystemCallError, e.message + " - Additional message"
              rescue SystemCallError => e
                puts e
              end
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        unknown error - Hello World! I am calling via raise expression - Additional message

        _, $rubyVMManager->stdOut->readAll());
    }
}
