<?php

namespace DupChallenge;

use DupChallenge\Controllers\AdminPagesController;
use DupChallenge\Controllers\DirectoryScannerController;
use DupChallenge\Controllers\Endpoints\StartScanEndpoint;
use DupChallenge\Controllers\Endpoints\TreeViewEndpoint;
use DupChallenge\Controllers\Endpoints\ScanStatusEndpoint;
use DupChallenge\Controllers\Crons\DirectoryScannerCron;
use DupChallenge\Controllers\ScannerStatusController;

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

        add_action('admin_init', [__CLASS__, 'hookAdminInit']);
        add_action('admin_menu', [__CLASS__, 'menuInit']);
        add_action('rest_api_init', [__CLASS__, 'registerRestEndpoints']);
        add_action(DirectoryScannerCron::EVENT_HOOK, [DirectoryScannerCron::getInstance(), 'hookCallback']);
        add_action(DirectoryScannerController::EVENT_HOOK, [__CLASS__, 'hookScanEvent']);
        add_action(DirectoryScannerController::ACTION_SCAN_START, [ScannerStatusController::getInstance(), 'updateStarted']);
        add_action(DirectoryScannerController::ACTION_SCAN_END, [ScannerStatusController::getInstance(), 'updateFinished']);
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

    /**
     * Hook scan event
     *
     * @return void
     */
    public static function hookScanEvent()
    {
        DirectoryScannerController::getInstance()->processScanChunk();
    }

    /**
     * Register rest endpoints
     *
     * @return void
     */
    public static function registerRestEndpoints()
    {
        StartScanEndpoint::getInstance()->register();
        TreeViewEndpoint::getInstance()->register();
        ScanStatusEndpoint::getInstance()->register();
    }
}
