<?php

namespace DupChallenge\Controllers;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Controllers\Tables\FileSystemNodesTable;
use DupChallenge\Controllers\TableController;

/**
 * Scanner status controller
 */
class ScannerStatusController
{
    use SingletonTrait;

    /**
     * Option name
     *
     * @var string
     */
    const OPTION_NAME = 'dup_challenge_scanner_status';

    /**
     * Get scanner status
     *
     * @return array<string, mixed> Scanner status
     */
    public function getStatus()
    {
        $status = get_option(self::OPTION_NAME);

        if (!$status || !is_array($status)) {
            $status = [
                'started' => false,
                'startedAt' => 0,
                'finished' => false,
                'finishedAt' => 0,
            ];
        }

        $status['totalScannedItems'] = $this->getTotalScannedItems();

        return $status;
    }

    /**
     * Update scanner started status
     *
     * @return void
     */
    public function updateStarted()
    {
        update_option(self::OPTION_NAME, [
            'started' => true,
            'startedAt' => time(),
            'finished' => false,
            'finishedAt' => 0,
            'totalScannedItems' => 0,
        ]);
    }

    /**
     * Update scanner finished status
     *
     * @return void
     */
    public function updateFinished()
    {
        $currentStatus = $this->getStatus();

        update_option(self::OPTION_NAME, [
            'started' => false,
            'startedAt' => $currentStatus['startedAt'],
            'finished' => true,
            'finishedAt' => time(),
            'totalScannedItems' => $this->getTotalScannedItems(),
        ]);
    }

    /**
     * Delete scanner status
     *
     * @return void
     */
    public function deleteStatus()
    {
        delete_option(self::OPTION_NAME);
    }

    /**
     * Get total scanned items
     *
     * @return int Total scanned items
     */
    public function getTotalScannedItems()
    {
        $tableName = FileSystemNodesTable::getInstance()->getName();

        return TableController::getInstance()->getRowCount($tableName);
    }
}
