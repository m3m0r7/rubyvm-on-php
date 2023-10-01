<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ToStringTest extends TestApplication
{
    public function testToString(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 65535.to_s
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
    }
}
