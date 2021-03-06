<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require '../config.php';

spl_autoload_register(function ($class) {

    if (file_exists('controllers/' . $class . '.php')) {
        require_once 'controllers/' . $class . '.php';
    } elseif (file_exists('../models/' . $class . '.php')) {
        require_once '../models/' . $class . '.php';
    } elseif (file_exists('../core/' . $class . '.php')) {
        require_once '../core/' . $class . '.php';
    } elseif (file_exists('../library/' . $class . '.php')) {
        require_once '../library/' . $class . '.php';
    }
});

$core = new Core();
$core->run();

