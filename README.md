# RubyVM on PHP


The RubyVM on PHP is implementation RubyVM written in PHP 100%.
RubyVM has not completely documentation and I was referred [Ruby source code](https://github.com/ruby/ruby) when contributing this project.

_Notice: This project is very ultra super hyper maximum experimental implementation_

_Notice: I tested Ruby version 3.2 only_

### See also
- https://github.com/ruby/ruby/blob/master/compile.c
- https://github.com/ruby/ruby/blob/master/vm.c
- https://github.com/ruby/ruby/blob/master/vm_exec.c

## DEMO

<img src="./docs/demo.gif" width="100%" />

## Quick start

1. Install via composer as following

```
$ composer require m3m0r7/rubyvm-on-php
```

2. Save the below code as `HelloWorld.rb`

```ruby
puts RubyVM::InstructionSequence.compile("puts 'HelloWorld!\n'", "HelloWorld.rb").to_binary
```

3. Output `.yarv` file as following

```shell
$ ruby HelloWorld.rb > HelloWorld.yarv
```

3. Create PHP file with below code and save as `HelloWorld.php`

```php
<?php
require __DIR__ . '/vendor/autoload.php';

// Instantiate RubyVM class
$rubyVM = new \RubyVM\VM\Core\Runtime\RubyVM(
    new \RubyVM\VM\Core\Runtime\Option(
        reader: new \RubyVM\VM\Stream\BinaryStreamReader(
            streamHandler: new \RubyVM\VM\Stream\FileStreamHandler(
                // Specify to want you to load YARV file
                __DIR__ . '/HelloWorld.yarv',
            ),
        ),

        // Choose Logger
        logger: new \Psr\Log\NullLogger(),
    ),
);

// Register kernel its each of Ruby Versions
$rubyVM->register(
    rubyVersion: \RubyVM\VM\Core\Runtime\RubyVersion::VERSION_3_2,
    kernelClass: \RubyVM\VM\Core\Runtime\Version\Ruby3_2\Kernel::class,
);

// Disassemble instruction sequence binary formatted and get executor
$executor = $rubyVM->disassemble(
    useVersion: \RubyVM\VM\Core\Runtime\RubyVersion::VERSION_3_2,
);

// Execute disassembled instruction sequence
$executor->execute();
```

4. Run `php HelloWorld.php` and you will get outputted `HelloWorld!` from RubyVM.

## Test

```
$ ./vendor/bin/phpunit tests/
```

## Linter

```
./vendor/bin/phpcbf src/ tests/
```

## Other my toys

- [PHPJava](https://github.com/php-java/php-java) - Implement a JVM written in PHP
- [nfc-for-php](https://github.com/m3m0r7/nfc-for-php) - A NFC Reader (Control a NFC hardware) written in PHP
- [PHPPython](https://github.com/m3m0r7/PHPPython) - Implement a PYC executor written in PHP
