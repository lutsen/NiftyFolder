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

	$view->offsetSet('site_title', SITE_TITLE);
	$view->offsetSet('site_descr', SITE_DESCR);

	if ( !empty(SITE_LOGO) ) {
		$view->offsetSet('site_logo', SITE_LOGO);
	}
	if ( !empty(HEADER_BACKGROUND) ) {
		$view->offsetSet('header_image', HEADER_IMAGE);
	}

	// Call-to-action
	if ( !empty(CTA_URL) ) {
		$view->offsetSet('cta_url', CTA_URL);
	}
	if ( !empty(CTA_TEXT) ) {
		$view->offsetSet('cta_text', CTA_TEXT);
	}
	if ( !empty(CTA_BUT) ) {
		$view->offsetSet('cta_but', CTA_BUT);
	}

	if ( !empty(SOCIAL_TEXT) ) {
		$view->offsetSet('social_text', SOCIAL_TEXT);
	}

	// Social links
	$social['facebook'] = SOCIAL_FACEBOOK;
	$social['github'] = SOCIAL_GITHUB;
	$social['instagram'] = SOCIAL_INSTAGRAM;
	$social['linkedin'] = SOCIAL_LINKEDIN;
	$social['researchgate'] = SOCIAL_RESEARCHGATE;
	$social['snapchat'] = SOCIAL_SNAPCHAT;
	$social['twitter'] = SOCIAL_TWITTER;
	$social['youtube'] = SOCIAL_YOUTUBE;
	foreach ($social as $key => $value) {
		if ( empty($value) ) {
			unset( $social[$key] );
		}
	}
	$view->offsetSet('social_links', $social);

	// Tree
	$view->addExtension(new QEEP\TwigTreeTag\Twig\Extension\TreeExtension());

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