#!/usr/bin/env php
<?php

use Deploy\Deploy;

chdir(dirname(__DIR__));
define('CLASS_DIR', './classes/');
spl_autoload_register('autoload');

// Composer autoloading
include './vendor/autoload.php';



$config = new Zend\Config\Config(include './config/config.php');
$deploy = new Deploy($config);

echo "Your PHP vesion is " . PHP_VERSION. "\n";

try {
    $deploy->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}


function autoload($classname){

    if (class_exists($classname)){
        return true;
    }

    $class_path = CLASS_DIR . $classname .'.php';

    if(file_exists($class_path)) {
        require_once($class_path);
    }
}