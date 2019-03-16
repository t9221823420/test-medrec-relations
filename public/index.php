<?php
defined('ENV_DEBUG') or define('ENV_DEBUG', true);
defined('ENV_DEV') or define('ENV_DEV', true);


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/Base.php';

$config = array_merge(
	require __DIR__ . '/../config/app.php'
);

( new \core\App( $config ) )->run();