<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class StringTest extends TestApplication
{
    public function testEmpty(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "".empty?
            puts "Hello World!".empty?
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testInclude(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "Hello World!".include? "World"
            puts "Hello World!".include? "Underground"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false

        _, $rubyVMManager->stdOut->readAll());
    }
}
