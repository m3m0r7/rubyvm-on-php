<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\GenericSyntax;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Exception\Raise;
use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class RaiseTest extends TestApplication
{
    public function testRaise(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            raise RuntimeError, "Hello World!"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $result = $executor->execute();
        $this->assertSame(ExecutedStatus::EXIT, $result->executedStatus);
        $this->assertMatchesRegularExpression(
            "/^[^:]+:-?\\d:in '<main>': Hello World! \\(\x1b\\[4mRuntimeError\x1b\\[0m\\)\$/",
            $rubyVMManager->stdErr->readAll(),
        );

        $this->assertSame(null, $result->returnValue);
        $this->assertInstanceOf(Raise::class, $result->threw);
    }
}
