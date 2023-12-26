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
class ToIntTest extends TestApplication
{
    public function testToInt(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts 65535.to_i
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame("65535\n", $rubyVMManager->stdOut->readAll());
    }
}
