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
class PutsTest extends TestApplication
{
    public function testPuts(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "Hello World!"
            puts "Hello World!".chars
            puts [1, 2, 3, 4, 5, 6]
            puts true
            puts false
            puts nil
            puts({ key1: "Hello!", key2: "World!"})
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        // NOTE: The IDE will be deleted one space, here is hack add one space.
        $space = ' ';
        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<_
        Hello World!
        H
        e
        l
        l
        o
        {$space}
        W
        o
        r
        l
        d
        !
        1
        2
        3
        4
        5
        6
        true
        false

        {:key1=>"Hello!", :key2=>"World!"}

        _, $rubyVMManager->stdOut->readAll());
    }
}
