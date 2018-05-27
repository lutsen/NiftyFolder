<?

/**
 * This file contains the settings for error reporting and different paths and URL to your NiftyFolder website.
 * Add your settings and path info and rename it to config.php for your site to work.
 */

/**
 * @var string[] An array with admin usernames (key) and their passwords (value).
 */
$users = array(
	'admin'	=> 'password'
);

/**
 * @var string[] An array the paths protected by the user password combination(s).
 */
$protected = array(
	'/admin'
);

/**
 * @const FOLDER_ID The folder-id of the root folder of your website on Google Drive.
 */
define('FOLDER_ID', '');

/**
 * @const ERROR_REPORTING Enable or disable error reporting.
 */
define('ERROR_REPORTING', false);

/**
 * @const ROOT_PATH The server path to the root directory of your NiftyFolder project (Should not have a trailing slash).
 */
define('ROOT_PATH', __DIR__);
/**
 * @const APP_PATH The server path to the public directory of your NiftyFolder project (Should not have a trailing slash).
 */
define('APP_PATH', __DIR__ . '/public');
/**
 * @const APP_URL The URL of your NiftyFolder project (Should not have a trailing slash).
 */
define('APP_URL', '');

?>