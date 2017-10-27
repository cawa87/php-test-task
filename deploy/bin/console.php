<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../src/Resources/config'));
$loader->load('parameters.yml');
$loader->load('common.yml');
$loader->load('command.yml');
$loader->load('operation/deploy.yml');

$deployCommand = $container->get('command.deploy');

$application = new Application();
$application->add($deployCommand);

$application->run();
