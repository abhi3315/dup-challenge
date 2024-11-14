<?php

namespace DupChallenge;

use DupChallenge\Controllers\TableController;
use DupChallenge\Controllers\Tables\FileSystemNodesTable;
use DupChallenge\Controllers\Tables\FileSystemClosureTable;

/**
 * Install class
 */
class Install
{
    /**
     * Register install hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin()) {
            register_activation_hook(DUP_CHALLENGE_FILE, array(__CLASS__, 'onActivation'));
        }
    }

    /**
     * Install plugin
     *
     * @return void
     */
    public static function onActivation()
    {
        // Create tables
        $tableController = TableController::getInstance();
        $tableController->createTable(FileSystemNodesTable::getInstance());
        $tableController->createTable(FileSystemClosureTable::getInstance());
    }
}
