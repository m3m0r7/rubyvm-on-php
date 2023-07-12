# RubyVM on PHP


The RubyVM on PHP is implementation RubyVM written in PHP 100%.

_Notice: This project is very ultra super hyper maximum experimental implementation._
_The project can execute only `puts` method in the Ruby_


## How to run

1. I will rewrite this project with PHP ecosystems. in until then, git clone this repository to be done preparation.
2. Use `RubyVM::InstructionSequence.compile` and get created `HelloWorld.yarb` with below shell command.

```ruby
puts RubyVM::InstructionSequence.compile("puts 'HelloWorld!\n'", "HelloWorld.rb").to_binary
```

```shell
ruby test.rb > HelloWorld.yarb
```

3. Create PHP code

```php
<?php
require __DIR__ . '/stream.php';
require __DIR__ . '/rubyvm.php';

$rubyVM = new RubyVM(
    new Stream(__DIR__ . '/HelloWorld.yarb'),
);

$iseq = $rubyVM->disassemble();


echo "RESULT ------------------\n";
$iseq->evaluate();

echo "\n";

echo "INFO --------------------\n";
var_dump($rubyVM);
```

And save as `test.php` above code.

4. Run `php test.php`
5. You will get below result

```
RESULT ------------------
HelloWorld!

INFO --------------------
object(RubyVM)#1 (8) {
  ["Compiled Ruby Version"]=>
  string(3) "3.2"
  ["YARB file"]=>
  string(3) "YES"
  ["File size"]=>
  int(232)
  ["Extra size"]=>
  int(0)
  ["ISeqListSize"]=>
  int(1)
  ["GlobalObjectListSize"]=>
  int(5)
  ["ISeqListOffset"]=>
  int(148)
  ["GlobalObjectListOffset"]=>
  int(212)
}
```