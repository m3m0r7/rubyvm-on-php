<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\BasicObject\Kernel\Object_\Enumerable;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class IntegerTest extends TestApplication
{
    public function testEven(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 3.even?
            puts 2.even?
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testOdd(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 3.odd?
            puts 2.odd?
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

    public function testZero(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 1.zero?
            puts 0.zero?
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true

        _, $rubyVMManager->stdOut->readAll());
    }
}
