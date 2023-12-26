<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Exception\ExitException;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ExitTest extends TestApplication
{
    public function testExit(): void
    {
        $this->expectException(ExitException::class);
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            exit
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $executor->execute();
    }
}
