<?php

namespace DupChallenge\Controllers;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Views\Main\MainPageView;
use DupChallenge\Views\Settings\SettingsPageView;
use DupChallenge\Interfaces\RestEndpointInterface;

/**
 * Singleton class controller for admin pages
 */
class AdminPagesController
{
    // Use trait to implement singleton pattern
    use SingletonTrait;

    const MAIN_PAGE_SLUG = 'duplicator-challenge';
    const SETTINGS_PAGE_SLUG = 'duplicator-challenge-settings';

    /**
     * Add admin javascripts
     *
     * @return void
     */
    public static function adminScripts()
    {
        if (!self::isPluginAdminPage()) {
            return;
        }

        wp_enqueue_script(
            'duplicator-challenge-admin-scripts',
            DUP_CHALLENGE_URL . '/dist/index.js',
            [],
            DUP_CHALLENGE_VERSION,
            true
        );

        wp_localize_script('duplicator-challenge-admin-scripts', 'dupChallengeData', [
            'restRootUrl' => esc_url_raw(rest_url(RestEndpointInterface::ENDPOINT_NAMESPACE)),
            'nonce' => wp_create_nonce('wp_rest'),
            'rootDir' => DUP_WP_ROOT_PATH,
            'currentPage' => sanitize_text_field((isset($_REQUEST['page']) ? $_REQUEST['page'] : '')),
        ]);
    }

    /**
     * Add admin styles
     *
     * @return void
     */
    public static function adminStyles()
    {
        if (!self::isPluginAdminPage()) {
            return;
        }

        wp_enqueue_style(
            'duplicator-challenge-admin-styles',
            DUP_CHALLENGE_URL . '/dist/index.css',
            [],
            DUP_CHALLENGE_VERSION
        );
    }

    /**
     * Check if current page is plugin admin page
     *
     * @return bool
     */
    public static function isPluginAdminPage()
    {
        $page = sanitize_text_field((isset($_REQUEST['page']) ? $_REQUEST['page'] : ''));
        $pages = [
            self::MAIN_PAGE_SLUG,
            self::SETTINGS_PAGE_SLUG,
        ];
        return in_array($page, $pages);
    }

    /**
     * Main page action
     *
     * @return void
     */
    public function mainPageAction()
    {
        MainPageView::renderMainPage();
    }

    /**
     * Settings page action
     *
     * @return void
     */
    public function settingsPageAction()
    {
        SettingsPageView::renderMainPage();
    }
}
