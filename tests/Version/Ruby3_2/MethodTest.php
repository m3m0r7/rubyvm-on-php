<?php
declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2;

use RubyVM\VM\Core\Runtime\Executor\ExecutedStatus;
use RubyVM\VM\Core\Runtime\RubyVersion;
use Tests\RubyVM\Helper\TestApplication;

class MethodTest extends TestApplication
{
    public function testPutsMethod(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< _
            puts "HelloWorld!"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble(RubyVersion::VERSION_3_2);

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute());
        $this->assertSame("HelloWorld!", $rubyVMManager->stdOut->readAll());
    }


}
