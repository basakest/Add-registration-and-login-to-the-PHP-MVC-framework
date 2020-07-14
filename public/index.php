<?php

/**
 * Front controller
 * 
 * PHP version 7.3.11
 */
require_once dirname(__DIR__) . "/vendor/autoload.php";

/**
 * the error
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * the session
 */
session_start();

/**
 * the router
 */
$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('login', ['controller' => 'login', 'action' => 'new']);
$router->add('logout', ['controller' => 'login', 'action' => 'sestroy']);
//don't add / before admin
//$router ->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$url = $_SERVER['QUERY_STRING'];

$router->dispatch($url);




