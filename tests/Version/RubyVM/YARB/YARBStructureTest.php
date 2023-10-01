<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\Ruby3_2\YARB;

use Tests\RubyVM\Helper\TestApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class YARBStructureTest extends TestApplication
{
    public function testHeader(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "HelloWorld!"
            _,
        );
        $rubyVMManager->rubyVM->runtime()->setup();
        $this->assertSame('3.2', $rubyVMManager->rubyVM->runtime()->rubyVersion());
        if ($this->isCI()) {
            $this->assertSame('x86_64-linux', $rubyVMManager->rubyVM->runtime()->rubyPlatform());
        } else {
            $this->assertSame('arm64-darwin22', $rubyVMManager->rubyVM->runtime()->rubyPlatform());
        }
    }

    public function testExtraData(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts "HelloWorld!"
            _,
            extraData: 'Extra Hello World!'
        );

        $rubyVMManager->rubyVM->runtime()->setup();
        $this->assertSame('Extra Hello World!', $rubyVMManager->rubyVM->runtime()->extraData());
    }
}
