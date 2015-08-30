<?php
// Settings to make all errors more obvious during testing
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require_once PROJECT_ROOT . '/vendor/autoload.php';

// #### PayPal SDK configuration
// Register the sdk_config.ini file in current directory
// as the configuration source.
if (!defined("PP_CONFIG_PATH")) {
    define("PP_CONFIG_PATH", __DIR__);
}

use \There4\Slim\Test\WebTestCase;


// Initialize our own copy of the slim application
class LocalWebTestCase extends WebTestCase
{
    public function getSlimInstance()
    {
        $app = new \Slim\Slim(array(
            'version' => '0.0.0',
            'debug' => false,
            'mode' => 'testing',
            'templates.path' => PROJECT_ROOT . '/templates'
        ));
        // Include our core application file
        require PROJECT_ROOT . '/app.php';

        return $app;
    }
};

