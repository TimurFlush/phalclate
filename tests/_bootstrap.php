<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('DATA_DIR', __DIR__ . '/_data');
define('FIXTURES_DIR', __DIR__ . '/_fixtures');

use Codeception\Util\Fixtures;
use Dotenv\Dotenv as EnvLoader;
use Phalcon\Loader as Loader;

/* Fixtures */
Fixtures::add('translations', require FIXTURES_DIR . '/translations.php');
Fixtures::add('languages', require FIXTURES_DIR . '/languages.php');

/* Environment */
EnvLoader::create(DATA_DIR)->load();

$loader = new Loader();
$loader->register();