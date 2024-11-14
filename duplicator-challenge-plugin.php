<?php

/**
 * Plugin Name: Duplicator Challenge Plugin
 * Plugin URI: https://github.com/abhi3315/dup-challenge
 * Description: Duplicator Challenge Plugin
 * Version: 1.0.0
 * Requires at least: 5.2
 * Tested up to: 6.2.2
 * Requires PHP: 5.6.20
 * Author: Abhishek Sharma
 * Author URI: https://github.com/abhi3315
 * Text Domain: dup-challenge
 */

defined('ABSPATH') || exit;

define('DUP_CHALLENGE_VERSION', '1.0.0');

define('DUP_CHALLENGE_PATH', __DIR__);
define('DUP_CHALLENGE_FILE', __FILE__);
define('DUP_CHALLENGE_URL', plugins_url('', DUP_CHALLENGE_FILE));

// Define the root path of the WordPress installation.
if (!defined('DUP_WP_ROOT_PATH')) {
    define('DUP_WP_ROOT_PATH', untrailingslashit(ABSPATH));
}

// Define chunk size for file scanning
if (!defined('DUP_CHUNK_SIZE')) {
    define('DUP_CHUNK_SIZE', 500);
}

// Define chunk processing time gap in seconds
if (!defined('DUP_CHUNK_PROCESSING_GAP')) {
    define('DUP_CHUNK_PROCESSING_GAP', 1);
}

require_once(DUP_CHALLENGE_PATH . '/src/Utils/Autoloader.php');
DupChallenge\Utils\Autoloader::register();
DupChallenge\Bootstrap::init();
