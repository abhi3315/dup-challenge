<?php

namespace DupChallenge\Controllers;

use DupChallenge\Abstracts\BaseController;
use DupChallenge\Views\Main\MainPageView;
use DupChallenge\Views\Settings\SettingsPageView;

/**
 * Singleton class controller for admin pages
 */
class AdminPagesController extends BaseController
{
    const MAIN_PAGE_SLUG = 'duplicator-challenge';
    const SETTINGS_PAGE_SLUG = 'duplicator-challenge-settings';

    /**
     * Class constructor
     */
    protected function __construct()
    {
    }

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
            DUP_CHALLENGE_URL . '/assets/js/admin.js',
            [],
            DUP_CHALLENGE_VERSION,
            true
        );
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
            DUP_CHALLENGE_URL . '/assets/css/admin.css',
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
