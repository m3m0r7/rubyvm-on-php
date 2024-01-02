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
class PrintTest extends TestApplication
{
    public function testPrint(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            print "Hello World!"
            print "\n"
            print "Hello World!".chars
            print "\n"
            print [1, 2, 3, 4, 5, 6]
            print "\n"
            print true
            print "\n"
            print false
            print "\n"
            print nil
            print "\n"
            print({ key1: "Hello!", key2: "World!"})
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        Hello World!
        ["H", "e", "l", "l", "o", " ", "W", "o", "r", "l", "d", "!"]
        [1, 2, 3, 4, 5, 6]
        true
        false

        {:key1=>"Hello!", :key2=>"World!"}
        _, $rubyVMManager->stdOut->readAll());
    }
}
