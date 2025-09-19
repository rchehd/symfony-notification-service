<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
    ])
    ->exclude('var')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                   => true,
        'array_syntax'               => ['syntax' => 'short'],
        'binary_operator_spaces'     => ['default' => 'align_single_space'],
        'no_unused_imports'          => true,
        'phpdoc_summary'             => false,
        'phpdoc_align'               => true,
        'phpdoc_separation'          => true,
        'phpdoc_trim'                => true,
        'phpdoc_order'               => true,
        'phpdoc_scalar'              => true,
        'phpdoc_to_comment'          => false,
        'no_empty_phpdoc'            => false,
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
