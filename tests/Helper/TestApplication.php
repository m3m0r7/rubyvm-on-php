<?php
declare(strict_types=1);

namespace Tests\RubyVM\Helper;

use PHPUnit\Framework\TestCase;
use RubyVM\VM\Core\Runtime\RubyVersion;
use RubyVM\VM\Core\Runtime\RubyVM;
use RubyVM\VM\Stream\StreamHandler;

class TestApplication extends TestCase
{
    protected function createRubyVMFromCode(string $code, string $binaryPath = 'ruby'): RubyVMManager
    {
        $handle = tmpfile();
        fwrite($handle, $code);
        $uri = stream_get_meta_data($handle)['uri'];

        $compilerHandle = tmpfile();
        fwrite(
            $compilerHandle, <<<_
        puts RubyVM::InstructionSequence.compile_file("{$uri}").to_binary
        _
        );
        $compilerRubyUri = stream_get_meta_data($compilerHandle)['uri'];

        exec("{$binaryPath} {$compilerRubyUri}", $output);
        $binary = (string) implode("\n", $output);

        $stdOut = new StreamHandler(fopen('php://memory', 'w+'));
        $stdIn = new StreamHandler(fopen('php://memory', 'w+'));
        $stdErr = new StreamHandler(fopen('php://memory', 'w+'));

        $rubyVM = new \RubyVM\VM\Core\Runtime\RubyVM(
            new \RubyVM\VM\Core\Runtime\Option(
                reader: new \RubyVM\VM\Stream\BinaryStreamReader(
                    streamHandler: new \RubyVM\VM\Stream\StringStreamHandler(
                        $binary,
                    ),
                ),
                logger: new \Psr\Log\NullLogger(),
                stdOut: $stdOut,
                stdIn: $stdIn,
                stdErr: $stdErr,
            ),
        );

        // Set default kernel
        $rubyVM->register(
            rubyVersion: \RubyVM\VM\Core\Runtime\RubyVersion::VERSION_3_2,
            kernelClass: \RubyVM\VM\Core\Runtime\Version\Ruby3_2\Kernel::class,
        );

        return new RubyVMManager(
            $rubyVM,
            $stdOut,
            $stdIn,
            $stdErr,
        );
    }
}
