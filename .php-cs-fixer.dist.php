<?php
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->append([__FILE__]);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    'concat_space' => ['spacing' => 'one'],
    'whitespace_after_comma_in_array' => true,
    'return_type_declaration' => [
        'space_before' => 'none'
    ],
    'method_argument_space' => true,
    'single_quote' => true,
    'cast_spaces' => ['space' => 'single'],
    'binary_operator_spaces' => ['default' => 'single_space'],
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'trailing_comma_in_multiline' => true,
])
->setFinder($finder)
;
