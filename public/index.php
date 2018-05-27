<?php

/**
 * This is the index file for your NiftyFolder website.
 *
 * The index.php file contains the configuration for Slim,
 * the error configuration serttings and the route files.
 */

// Make sure to add the missing details to the config.php file.
require __DIR__.'/../config.php'; // Note: change this path if your Lagan project is in a subdirectory

// Error reporting
if (ERROR_REPORTING) {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', '1');
	ini_set('html_errors', '1');
}

// Composer autoloader
require ROOT_PATH.'/vendor/autoload.php';

/**
 * NiftyFolder autoloader
 *
 * @param string $class_name The name of the class to load.
 */
function customAutoload( $class_name ) {
	// Load models, controllers
	$paths = array(
		'/models/',
		'/controllers/'
	);

	foreach ($paths as $path) {
		// Handle backslashes in namespaces
		if ( strpos( $class_name, '\\' ) ) {
			$file = ROOT_PATH.$path.substr( $class_name, strrpos( $class_name, '\\' )+1 ).'.php';
		} else {
			$file = ROOT_PATH.$path.$class_name.'.php';
		}
		if ( file_exists( $file ) ) {
			require_once $file;
			return;
		}
	}
}
spl_autoload_register('customAutoload');

// ### SLIM SETUP ### //
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Required for flash messages in Slim: start a new session 
session_start();

$app = new \Slim\App(["settings" => [
	'displayErrorDetails' => ERROR_REPORTING
]]);

$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
	$view = new \Slim\Views\Twig([
		ROOT_PATH.'/templates'
	],
	[
	//	'cache' => ROOT_PATH.'/cache'
	]);

	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

	// General variables to render views
	$view->offsetSet('app_url', APP_URL);
	$view->offsetSet('page_url', APP_URL . $c['request']->getUri()->getPath());

	// Tree
	$view->addExtension(new JordanLev\TwigTreeTag\Twig\Extension\TreeExtension());

	return $view;
};

// Register Slim flash messages
$container['flash'] = function () {
	return new \Slim\Flash\Messages();
};

// Add HTTP Basic Authentication middleware
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
	'path' => $protected, // Defined in config.php
	'secure' => true,
	'relaxed' => ['localhost'],
	'users' => $users // Defined in config.php
]));



// ### ROUTES ### //

// Include all the route files.
$routeFiles = glob(ROOT_PATH.'/routes/*.php');
foreach( $routeFiles as $routeFile ) {
	require_once $routeFile;
}

$app->run();

?>