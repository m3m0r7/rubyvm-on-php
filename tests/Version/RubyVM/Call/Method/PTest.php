<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class PTest extends TestApplication
{
    public function testSimpleP(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p "Hello World!"
            p [1, 2, 3, 4, 5, 6]
            p true
            p false
            p nil
            p({ key1: "Hello!", key2: "World!"})
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        "Hello World!"
        [1, 2, 3, 4, 5, 6]
        true
        false
        nil
        {:key2=>"World!", :key1=>"Hello!"}

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testDefaultClassP(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            p -> { puts "Hello World!" }
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertMatchesRegularExpression(
            '/#<Proc:0x[0-9a-f]+ [^:]+:-?\d+>/',
            $rubyVMManager->stdOut->readAll(),
        );
    }
}
