<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class NilTest extends TestApplication
{
    public function testObjectIsNil(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            var1 = nil
            var2 = "text"

            puts nil.nil?
            puts var1.nil?
            puts var2.nil?
            puts 1.nil?
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(<<<'_'
        true
        true
        false
        false

        _, $rubyVMManager->stdOut->readAll());
    }
}
