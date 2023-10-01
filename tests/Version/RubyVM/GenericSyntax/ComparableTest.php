<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ComparableTest extends TestApplication
{
    public function testCompareLessThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 < 20).to_s
            puts (100 < 20).to_s
            puts (20 < 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false
        false

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareLessOrEqualsThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 <= 20).to_s
            puts (100 <= 20).to_s
            puts (20 <= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        false
        true

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 > 20).to_s
            puts (100 > 20).to_s
            puts (20 > 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true
        false

        _, $rubyVMManager->stdOut->readAll());
    }

    public function testCompareGreaterOrEqualsThan(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts (1 >= 20).to_s
            puts (100 >= 20).to_s
            puts (20 >= 20).to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        false
        true
        true

        _, $rubyVMManager->stdOut->readAll());
    }
}
