<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/App',
        __DIR__ . '/Framework',
        __DIR__ . '/public',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align'],
        'braces_position' => ['anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        'concat_space' => ['spacing' => 'one'],
        'new_with_braces' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
