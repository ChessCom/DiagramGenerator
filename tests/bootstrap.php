<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install"?');
}

$loader = require $autoloadFile;

// Copy Symfony's way of loading doctrine annotations
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
