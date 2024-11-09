<?php

namespace DupChallenge;

use DupChallenge\Controllers\TableController;
use DupChallenge\Controllers\Tables\FileSystemNodesTable;
use DupChallenge\Controllers\Tables\FileSystemClosureTable;

/**
 * Uninstall class
 */
class Unistall
{
    /**
     * Register unistall hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin()) {
            register_deactivation_hook(DUP_CHALLENGE_FILE, array(__CLASS__, 'deactivate'));
        }
    }

    /**
     * Deactivation Hook
     *
     * @return void
     */
    public static function deactivate()
    {
		$tableController = TableController::getInstance();

		/**
		 * Drop closure table first as it has a foreign key constraint on nodes table
		 */
		$tableController->dropTable(FileSystemClosureTable::getInstance()->getName());
		$tableController->dropTable(FileSystemNodesTable::getInstance()->getName());
    }
}
