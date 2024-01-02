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
class DirTest extends TestApplication
{
    public function testPwd(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts Dir.pwd + "\n"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $dir = getcwd();
        $this->assertSame("{$dir}\n", $rubyVMManager->stdOut->readAll());
    }

    public function testCount(): void
    {
        $rubyVMManager = $this->createRubyVMFromCode(
            <<< '_'
            puts Dir.new(Dir.pwd).count.to_s + "\n"
            _,
        );

        $executor = $rubyVMManager
            ->rubyVM
            ->disassemble();

        $this->assertSame(ExecutedStatus::SUCCESS, $executor->execute()->executedStatus);
        $count = count(iterator_to_array(new \DirectoryIterator(getcwd())));
        $this->assertSame("{$count}\n", $rubyVMManager->stdOut->readAll());
    }
}
