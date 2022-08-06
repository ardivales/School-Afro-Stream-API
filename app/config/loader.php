<?php

$loader = new \Phalcon\Loader();
// Load composer vendor stuff
$loader->registerFiles( [ BASE_PATH . "/vendor/autoload.php" ] )->register();
$loader->registerNamespaces(
  [
    'App\Services'    => realpath(__DIR__ . '/../services/'),
    'App\Controllers' => realpath(__DIR__ . '/../controllers/'),
    'App\Models'      => realpath(__DIR__ . '/../models/'),
  ]
);

$loader->register();
