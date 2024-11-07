<?php

namespace DupChallenge;

use DupChallenge\Controllers\AdminPagesController;
use DupChallenge\Controllers\FileScannerController;

class Bootstrap
{
    /**
     * Init plugin
     *
     * @return void
     */
    public static function init()
    {
        Install::register();
        Unistall::register();

        FileScannerController::getInstance();

        add_action('admin_init', [__CLASS__, 'hookAdminInit']);
        add_action('admin_menu', [__CLASS__, 'menuInit']);
    }

    /**
     * Init admin
     *
     * @return void
     */
    public static function hookAdminInit()
    {
        add_action('admin_enqueue_scripts', [ AdminPagesController::class, 'adminScripts' ]);
        add_action('admin_enqueue_scripts', [ AdminPagesController::class, 'adminStyles' ]);
    }

    /**
     * Init menu
     *
     * @return void
     */
    public static function menuInit()
    {
        add_menu_page(
            __('Dup Challenge', 'dup-challenge'),
            __('Dup Challenge', 'dup-challenge'),
            'manage_options',
            AdminPagesController::MAIN_PAGE_SLUG,
            [AdminPagesController::getInstance(), 'mainPageAction'],
            'dashicons-admin-generic',
            100
        );

        add_submenu_page(
            AdminPagesController::MAIN_PAGE_SLUG,
            __('Settings', 'dup-challenge'),
            __('Settings', 'dup-challenge'),
            'manage_options',
            AdminPagesController::SETTINGS_PAGE_SLUG,
            [AdminPagesController::getInstance(), 'settingsPageAction']
        );
    }
}
