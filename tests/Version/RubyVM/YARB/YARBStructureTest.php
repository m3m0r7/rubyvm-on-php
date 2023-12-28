<?php

declare(strict_types=1);

namespace Tests\RubyVM\Version\RubyVM\YARB;

use RubyVM\VM\Core\Runtime\Kernel\Ruby3_3\Kernel as Ruby3_3_Kernel;
use RubyVM\VM\Stream\Endian;
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
        $this->assertSame("{$this->major}.{$this->minor}", $rubyVMManager->rubyVM->runtime()->rubyVersion());

        // TODO: Fix to be flexible when using any ruby version
        if ("{$this->major}.{$this->minor}" === '3.3') {
            $this->assertSame(null, $rubyVMManager->rubyVM->runtime()->rubyPlatform());

            /**
             * @var Ruby3_3_Kernel $kernel
             */
            $kernel = $rubyVMManager->rubyVM->runtime()->kernel();
            $this->assertSame(Endian::LITTLE_ENDIAN, $kernel->endian());
            $this->assertSame(0, $kernel->wordSize());
        } elseif ($this->isCI()) {
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
