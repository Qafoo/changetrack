#!/usr/bin/env php
<?php

if ((!@include __DIR__ . '/../../../../../autoload.php')
    && (!@include __DIR__ . '/../../../vendor/autoload.php')) {
    die("You must set up the project dependencies, run composer install.\n");
}

use Qafoo\ChangeTrack\Development\PharCompiler;

error_reporting(-1);
ini_set('display_errors', 1);

try {
    $compiler = new PharCompiler(__DIR__ . '/../../../');
    $compiler->compile();
} catch (\Exception $e) {
    echo "$e\n";
    exit(1);
}
