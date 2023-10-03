<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\Call\Method;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
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
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            exit
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $result = $executor->execute();
        $this->assertSame(ExecutedStatus::THREW_EXCEPTION, $result->executedStatus);
        $this->assertInstanceOf(ExitException::class, $result->threw);
    }
}
