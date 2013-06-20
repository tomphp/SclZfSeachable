<?php

chdir(__DIR__ . '/../../../');

include 'init_autoloader.php';

$loader->add('SclObjectManager\\', __DIR__ . '/src/');

$bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');
