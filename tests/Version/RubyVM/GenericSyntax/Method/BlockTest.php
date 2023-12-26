<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\GenericSyntax\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class BlockTest extends TestApplication
{
    public function testCallBlock(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def block_def
              yield "Hello World", "!"
            end

            block_def do | text1, text2 |
              puts text1 + text2
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCallDuplicatedBlock(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            def block_def
              yield "Hello World", "!"
            end

            block_def do | text1, text2 |
              puts text1 + text2
            end

            block_def do | text1, text2 |
              puts text1 + text2
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World!
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }
}
