<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Slim\Slim;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$isDevMode = true;
$mode = isset($mode) ? $mode : 'production';
$rootPath = dirname(__DIR__);
//config
$config = array(
    'logger' => '',
    'logger_name' => 'GALGOTIASUNIVERSITY',
    'log_file' => 'logs/api.log',
    'log_signal' => Logger::DEBUG, //minimum signal lvl to be logged (debug,info,notice,warning,critical
    'app_path' => $rootPath . 'v1/src',
);


//slim instance
$app = new Slim(array(
    'debug' => $isDevMode,
    'mode' => $mode
        ));


// DI loggger
$app->container->singleton('log', function () use ($config) {
    // create a log channel
    $log = new Logger($config['logger_name']);
    $log->pushHandler(new StreamHandler($config['log_file'], $config['log_signal']));
    return $log;
});

//setup middleware
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());
