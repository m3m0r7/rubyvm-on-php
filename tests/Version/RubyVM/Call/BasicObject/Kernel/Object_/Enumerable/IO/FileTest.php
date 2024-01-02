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
class FileTest extends TestApplication
{
    public function testRead(): void
    {
        $file = __FILE__;

        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts File.read("{$file}") + "\n"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $this->assertSame(file_get_contents($file) . "\n", $rubyVMManager->stdOut->readAll());
    }
}
