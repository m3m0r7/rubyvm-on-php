<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class BlockSyntaxTest extends TestApplication
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
            ->disassemble(RubyVersion::VERSION_3_2);

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
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World!
        Hello World!

        _, $rubyVMManager->stdOut->readAll());
    }
}
