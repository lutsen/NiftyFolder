<?

/**
 * This file contains:
 * - Your admin username(s) and password(s)
 * - The settings for error reporting
 * - Different paths and URL to your NiftyFolder website
 * - Strings and URLs used in the NiftyFolder template
 *
 * Add your settings, and rename this file to config.php for your site to work.
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



/**
 * @const SITE_TITLE The title of your website, used in most templates.
 */
define('SITE_TITLE', 'Nifty Folder');
/**
 * @const SITE_DESCR The description of your website, used in some templates.
 */
define('SITE_DESCR', 'Turn any folder on your Google Drive into a website in minutes');
/**
 * @const SITE_LOGO The URL of the logo image. Leave empty to display no logo.
 */
define('SITE_LOGO', 'theme/img/logo.png');
/**
 * @const HEADER_BACKGROUND The URL of header background image. Leave empty to display no background image.
 */
define('HEADER_BACKGROUND', 'theme/img/header.jpg');


/**
 * @const CTA_URL The URL the call-to-action button links to. To display no call-to-action button and text, leave CTA_URL empty.
 */
define('CTA_URL', 'https://confirmsubscription.com/h/j/1F6EF1371725EE60');
/**
 * @const CTA_TEXT
 */
define('CTA_TEXT', 'Subscribe to our newsletter:');
/**
 * @const CTA_BUT
 */
define('CTA_BUT', 'Subscribe');


/**
 * @const SOCIAL_TEXT The text above the social links.
 */
define('SOCIAL_TEXT', 'Follow us on');


/**
 * You can add links to your social media below. If an URL is left blank, the link won't be displayed.
 */

/**
 * @const SOCIAL_FACEBOOK
 */
define('SOCIAL_FACEBOOK', '');
/**
 * @const SOCIAL_GITHUB
 */
define('SOCIAL_GITHUB', 'https://github.com/lutsen/niftyfolder');
/**
 * @const SOCIAL_INSTAGRAM
 */
define('SOCIAL_INSTAGRAM', '');
/**
 * @const SOCIAL_LINKEDIN
 */
define('SOCIAL_LINKEDIN', 'https://www.linkedin.com/in/lutsen/');
/**
 * @const SOCIAL_RESEARCHGATE
 */
define('SOCIAL_RESEARCHGATE', '');
/**
 * @const SOCIAL_SNAPCHAT
 */
define('SOCIAL_SNAPCHAT', '');
/**
 * @const SOCIAL_TWITTER
 */
define('SOCIAL_TWITTER', 'https://twitter.com/lutsen');
/**
 * @const SOCIAL_YOUTUBE
 */
define('SOCIAL_YOUTUBE', '');


?>