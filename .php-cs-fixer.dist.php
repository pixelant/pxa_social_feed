<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
->in([
  __DIR__ . '/Classes',
  __DIR__ . '/Tests',
])
->name('*.php')
->ignoreDotFiles(true)
->ignoreVCS(true);
return $config;
