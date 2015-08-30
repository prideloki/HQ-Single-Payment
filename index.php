<?php
require 'vendor/autoload.php';
date_default_timezone_set('UTC');
// #### PayPal SDK configuration
// Register the sdk_config.ini file in current directory
// as the configuration source.
if (!defined("PP_CONFIG_PATH")) {
    define("PP_CONFIG_PATH", __DIR__);
}
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'log.enabled' => true,
    'log.level' => \Slim\Log::WARN
));

require_once __DIR__.'/app.php';

$app->run();
