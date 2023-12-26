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
class RegexpTest extends TestApplication
{
    public function testRegExpWithMatched(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /is Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        matched!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testRegExpWithUnmatched(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /was Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        unmatched!

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testContinuingRegExp(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            str = "This is Regexp"
            if /is Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end

            if /was Regexp/ =~ str
              puts "matched!"
            else
              puts "unmatched!"
            end

            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        matched!
        unmatched!

        _, $rubyVMManager->stdOut->readAll());
    }
}
