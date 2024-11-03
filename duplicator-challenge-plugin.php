<?php

/**
 * Plugin Name: Duplicator Challenge Plugin
 * Plugin URI:
 * Description: Duplicator Challenge Plugin
 * Version: 1.0.0
 * Requires at least: 5.2
 * Tested up to: 6.2.2
 * Requires PHP: 5.6.20
 * Author:
 * Author URI:
 * Update URI:
 * Text Domain: dup-challenge
 */

defined('ABSPATH') || exit;

define('DUP_CHALLENGE_VERSION', '1.0.0');

define('DUP_CHALLENGE_PATH', __DIR__);
define('DUP_CHALLENGE_FILE', __FILE__);
define('DUP_CHALLENGE_URL', plugins_url('', DUP_CHALLENGE_FILE));

// Define the root path of the WordPress installation.
if (!defined('DUP_WP_ROOT_PATH')) {
	define('DUP_WP_ROOT_PATH', ABSPATH);
}

require_once(DUP_CHALLENGE_PATH . '/src/Utils/Autoloader.php');
DupChallenge\Utils\Autoloader::register();
DupChallenge\Bootstrap::init();
