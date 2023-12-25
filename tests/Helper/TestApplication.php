<?php

declare(strict_types=1);

namespace Tests\RubyVM\Helper;

use PHPUnit\Framework\TestCase;
use RubyVM\VM\Core\YARV\RubyVersion;
use RubyVM\VM\Stream\StreamHandler;

/**
 * @internal
 *
 * @coversNothing
 */
class TestApplication extends TestCase
{
    protected int $major = -1;
    protected int $minor = -1;
    protected int $patch = -1;

    protected function createRubyVMFromCode(string $code, string $extraData = '', string $binaryPath = 'ruby'): RubyVMManager
    {
        $handle = tmpfile();
        if ($handle === false) {
            throw new \RuntimeException('tmpfile did not created');
        }

        if ($this->major === -1) {
            $version = sscanf(exec("{$binaryPath} -v") ?: 'ruby 3.2.0', 'ruby %d.%d.%d');
            if (!is_array($version)) {
                throw new \RuntimeException('The version is incorrect6');
            }

            [$this->major, $this->minor, $this->patch] = $version;
        }

        fwrite($handle, $code);
        $uri = stream_get_meta_data($handle)['uri'];

        $compilerHandle = tmpfile();
        if ($compilerHandle === false) {
            throw new \RuntimeException('tmpfile did not created');
        }

        fwrite(
            $compilerHandle,
            <<<_
            puts RubyVM::InstructionSequence.compile_file("{$uri}").to_binary("{$extraData}")
            _
        );
        $compilerRubyUri = stream_get_meta_data($compilerHandle)['uri'];

        exec("{$binaryPath} {$compilerRubyUri}", $output);
        $binary = implode("\n", $output);

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

        $rubyVM->setDefaultVersion(
            RubyVersion::from("{$this->major}.{$this->minor}"),
        );

        return new RubyVMManager(
            $rubyVM,
            $stdOut,
            $stdIn,
            $stdErr,
        );
    }

    protected function isCI(): bool
    {
        return getenv('ENV') === 'ci';
    }
}
