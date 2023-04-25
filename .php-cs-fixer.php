<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(["vendor"])
    ;

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setUsingCache(false)
    ->setFinder($finder)
    ;