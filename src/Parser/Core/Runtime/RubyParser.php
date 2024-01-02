<?php

declare(strict_types=1);

namespace RubyVM\Parser\Core\Runtime;

use RubyVM\Parser\Core\Runtime\Essential\RubyParserInterface;
use RubyVM\Parser\Exception\RuntimeException;
use RubyVM\RubyVersion;

class RubyParser implements RubyParserInterface
{
    final public const DEFAULT_VERSION = RubyVersion::VERSION_3_3;

    protected RubyVersion $specifiedDefaultVersion = self::DEFAULT_VERSION;

    /**
     * @var array<string, Runtime>
     */
    protected array $registeredRuntimes = [];

    public function __construct(public readonly OptionInterface $option) {}

    public function parse(RubyVersion $useVersion = null): never
    {
        throw new RuntimeException('Not implemented yet');
    }
}
