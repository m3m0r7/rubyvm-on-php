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
class CaseWhenTest extends TestApplication
{
    public function testCase(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            arr = 3

            case arr
            when 1 then
              puts "1"
            when 2 then
              puts "2"
            when 3 then
              puts "3"
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        3

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCaseToElse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            arr = 5
            case arr
            when 1 then
              puts "1"
            when 2 then
              puts "2"
            when 3 then
              puts "3"
            else
              puts "else"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        else

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCaseToNonElse(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            arr = 5
            case arr
            when 1 then
              puts "1"
            when 2 then
              puts "2"
            when 3 then
              puts "3"
            end

            puts arr
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        5

        _, $rubyVMManager->stdOut->readAll());
    }
}
