<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['config', 'vendor'])
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12'                        =>  true,
    'array_syntax'                  =>  ['syntax' => 'short'],
    'ordered_imports'               =>  ['sort_algorithm' => 'length'],
    'no_unused_imports'             =>  true,
    'trailing_comma_in_multiline'   =>  true,
])->setFinder($finder);
