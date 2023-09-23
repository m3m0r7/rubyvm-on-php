# RubyVM on PHP


The RubyVM on PHP is implementation RubyVM written in PHP 100%.
Completely documentation not exists how to implement RubyVM and I was referred [Ruby source code](https://github.com/ruby/ruby) when contributing this project.

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
    rubyVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
    kernelClass: \RubyVM\VM\Core\Runtime\Version\Ruby3_2\Kernel::class,
);

// Disassemble instruction sequence binary formatted and get executor
$executor = $rubyVM->disassemble(
    useVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
);

// Execute disassembled instruction sequence
$executor->execute();
```

4. Run `php HelloWorld.php` and you will get outputted `HelloWorld!` from RubyVM.

## Call defined ruby method on PHP

1. Create ruby code as below:

```ruby
def callFromPHP
  puts "Hello World from Ruby!"
end
```

And then, save file as `test.rb`

2. Compile to YARV as below:

```
$ ruby -e "puts RubyVM::InstructionSequence.compile_file('test.rb').to_binary" > test.yarv
```

3. Call ruby method on PHP as below:
```php
<?php
require __DIR__ . '/vendor/autoload.php';

// Instantiate RubyVM class
$rubyVM = new \RubyVM\VM\Core\Runtime\RubyVM(
    new \RubyVM\VM\Core\Runtime\Option(
        reader: new \RubyVM\VM\Stream\BinaryStreamReader(
            streamHandler: new \RubyVM\VM\Stream\FileStreamHandler(
                // Specify to want you to load YARV file
                __DIR__ . '/test.yarv',
            ),
        ),

        // Choose Logger
        logger: new \Psr\Log\NullLogger(),
    ),
);

// Register kernel its each of Ruby Versions
$rubyVM->register(
    rubyVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
    kernelClass: \RubyVM\VM\Core\Runtime\Version\Ruby3_2\Kernel::class,
);

// Disassemble instruction sequence binary formatted and get executor
$executor = $rubyVM->disassemble(
    useVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
);

// Execute disassembled instruction sequence
$executed = $executor->execute();

// Call Ruby method as below code.
// In this case, you did define method name is `callFromPHP`.
$executed->context()->callFromPHP();
```

You will get to output `Hello World from Ruby!`.
In addition case, maybe you want to pass arguments. of course, it is available on.
First time, to modify previous code as below.

```ruby
def callFromPHP(text)
  puts text
end
```

Second time, to modify PHP code `$executed->context()->callFromPHP()` as following:


```php
$executed->context()->callFromPHP('Hello World! Here is passed an argument from PHP!')
```

You will get to output `Hello World! Here is passed an argument from PHP`.

## Use an executor debugger

The RubyVM on PHP is provided an executor debugger that can display processed an INSN and anymore into a table as following:


```
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| PC  | CALLEE                         | INSN                           | PREVIOUS STACKS                                              | LOCAL TABLES |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 0   | <main>                         | [0x12] putself                 |                                                              |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 1   | <main>                         | [0x15] putstring               | OperandEntry<Main#0>                                         |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 3   | <main>                         | [0x33] opt_send_without_block  | OperandEntry<Main#0>, OperandEntry<StringSymbol(Hello World! |              |
|     |                                | (Main#puts(Hello World!))      | )#1>                                                         |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 5   | <main>                         | [0x3c] leave                   | OperandEntry<NilSymbol(<nil>)#0>                             |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
```

If you want to display above table then add below code from the Quick start.

_Notice: The executor debugger is using a lot of memories. We recommend to use disabling ordinarily. In depending on the case, may be using `-d memory_limit=NEEDING_MEMORY_BYTES` parameters to be working when calling `php` command_

```php
// Disassemble instruction sequence binary formatted and get executor
$executor = $rubyVM->disassemble(
    useVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
);

// Enable recording processed sequences with using `enableProcessedRecords` method.
$executor->enableProcessedRecords(true)->execute();

// You can display processed an INSN table when adding below code
$executor->debugger()->showExecutedOperations();
```


### Breakpoint

The RubyVM on PHP is providing breakpoint. The breakpoint is available to confirm to process a sequence step by step.
Which collect previous stacks, registered local tables and so on. this is required debugging this project.

```php
// Disassemble instruction sequence binary formatted and get executor
$executor = $rubyVM->disassemble(
    useVersion: \RubyVM\VM\Core\YARV\RubyVersion::VERSION_3_2,
);

// Enable breakpoint with using `enableBreakPoint` method.
$executor->enableBreakPoint(true)->execute();
```

When you enabled breakpoint, displays as below:

```
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| PC  | CALLEE                         | INSN                           | PREVIOUS STACKS                                              | LOCAL TABLES |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 0   | <main>                         | [0x12] putself                 |                                                              |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 1   | <main>                         | [0x15] putstring               | OperandEntry<Main#0>                                         |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 3   | <main>                         | [0x33] opt_send_without_block  | OperandEntry<Main#0>, OperandEntry<StringSymbol(Hello World! |              |
|     |                                | (Main#puts(Hello World!))      | )#1>                                                         |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
| 5   | <main>                         | [0x3c] leave                   | OperandEntry<NilSymbol(<nil>)#0>                             |              |
+-----+--------------------------------+--------------------------------+--------------------------------------------------------------+--------------+
Current INSN: putstring(0x15)
Previous Stacks: [total: 1, OperandEntry<Main>]#966
Previous Local Tables: []
Current Stacks: [total: 2, OperandEntry<Main>, OperandEntry<StringSymbol@HelloWorld!>]#561
Current Local Tables: []

Enter to next step (y/n/q): <INPUT_YOU_EXPECTING_NEXT_STEP>
```


## Custom method

The RubyVM on PHP has custom method in the main context.
Try to call `phpinfo` as below Ruby code on the RubyVM on PHP:

```ruby
phpinfo
```

Then you got displayed `PHP Version: 8.2.7`

## Test

```
$ ./vendor/bin/phpunit tests/
```

## Linter

```
./vendor/bin/php-cs-fixer fix --allow-risky=yes
```

## How to contribute

1) Build your ruby environment from source code with `-DIBF_ISEQ_DEBUG` flag

```
$ git clone git@github.com:ruby/ruby.git
$ mkdir build && cd build
$ ../configure cppflags="-DIBF_ISEQ_DEBUG=1"
$ make -j16
```

2) When you built ruby environment, you will got `vm.inc` file which is wrote how to execute each INSN commands

3) You can get logging at `ibf_load_**` when running ruby code as following

```
...omitted

ibf_load_object: type=0x15 special=1 frozen=1 internal=1      // The type is a FIX_NUMBER (2)
ibf_load_object: index=0x3 obj=0x5
ibf_load_object: list=0xf0 offsets=0x12b80fcf0 offset=0xe1
ibf_load_object: type=0x15 special=1 frozen=1 internal=1      // The type is a FIX_NUMBER (3)
ibf_load_object: index=0x4 obj=0x7
ibf_load_object: list=0xf0 offsets=0x12b80fcf0 offset=0xcd
ibf_load_object: type=0x5 special=0 frozen=1 internal=0       // The type is a STRING SYMBOL (puts)

...omitted
```

The above logs is created below example code:

```ruby
puts 1 + 2 + 3
```

4) Refer it and now you can contribute to implement INSN command in the RubyVM on PHP

## Other my toys

- [PHPJava](https://github.com/php-java/php-java) - Implement a JVM written in PHP
- [nfc-for-php](https://github.com/m3m0r7/nfc-for-php) - A NFC Reader (Control a NFC hardware) written in PHP
- [PHPPython](https://github.com/m3m0r7/PHPPython) - Implement a PYC executor written in PHP
