<?php

namespace DupChallenge\Controllers;

use SPLFileInfo;
use Exception;
use RecursiveDirectoryIterator;
use DupChallenge\Utils\ScannerQueueItem;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\ScannerInterface;
use DupChallenge\Interfaces\QueueInterface;
use DupChallenge\Interfaces\TableControllerInterface;
use DupChallenge\Controllers\Tables\FileSystemNodesTable;
use DupChallenge\Controllers\Tables\FileSystemClosureTable;
use DupChallenge\Controllers\ScanQueueController;

/**
 * Directory scanner controller
 */
class DirectoryScannerController implements ScannerInterface
{
    use SingletonTrait;

    const EVENT_NAME = 'dup_challenge_process_scan_chunk';
    const ACTION_SCAN_START = 'dup_challenge_scan_start';
    const ACTION_SCAN_COMPLETE = 'dup_challenge_scan_complete';

    /**
     * Queue
     *
     * @var QueueInterface
     */
    protected $queue;

    /**
     * Table controller
     *
     * @var TableControllerInterface
     */
    protected $tableController;

    /**
     * Chunk size
     *
     * @var int
     */
    protected $chunkSize;

    /**
     * Chunk processing gap
     *
     * @var int
     */
    protected $chunkProcessingGap;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->queue = ScanQueueController::getInstance();
        $this->tableController = TableController::getInstance();
        $this->chunkSize = DUP_CHUNK_SIZE;
        $this->chunkProcessingGap = DUP_CHUNK_PROCESSING_GAP;
    }

    /**
     * @inheritDoc
     *
     * @param string $rootPath The root path to start the scan
     *
     * @return void
     */
    public function startScanJob($rootPath = DUP_WP_ROOT_PATH)
    {
        if (!is_dir($rootPath)) {
            return;
        }

        if (!$this->cleanup()) {
            $this->logError(__('Failed to cleanup tables', 'dup-challenge'));
            return;
        }

		$root = new SPLFileInfo($rootPath);

        $this->queue->enqueue(new ScannerQueueItem($root->getPathname(), $root->getFilename(), $root->getType()));
        $this->queue->saveState();

        if (!wp_next_scheduled(self::EVENT_NAME)) {
            wp_schedule_single_event(time() + $this->chunkProcessingGap, self::EVENT_NAME);
        }

        do_action(self::ACTION_SCAN_START);
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function processScanChunk()
    {
        $this->queue->loadState();

        if ($this->queue->isEmpty()) {
            $this->finalizeScan();
            return;
        }

        $processedItemCount = 0;

        while (!$this->queue->isEmpty() && $processedItemCount < $this->chunkSize) {
            $currentItem = $this->queue->dequeue();

            if ($this->isProcessable($currentItem)) {
                $this->processItem($currentItem);
                $processedItemCount++;
            }

            $processedItemCount++;
        }

        $this->queue->saveState();
        wp_schedule_single_event(time() + $this->chunkProcessingGap, self::EVENT_NAME);
    }

    /**
     * Check if an item is processable
     *
     * @param mixed $item The item to check
     *
     * @return bool True if the item is processable, false otherwise
     */
    private function isProcessable(mixed $item)
    {
        return $item instanceof ScannerQueueItem && $item->hasRetries();
    }

    /**
     * Process an item
     *
     * @param ScannerQueueItem $item The item to process
     *
     * @return void
     */
    private function processItem(ScannerQueueItem $item)
    {
        $item->isDir() ? $this->processDirectory($item) : $this->processFile($item);
    }

    /**
     * Process a directory
     *
     * @param ScannerQueueItem $item The item to process
     *
     * @return void
     */
    private function processDirectory(ScannerQueueItem $item)
    {
        $nodeId = $this->insertNode($item);

        // If the node was not inserted, re-enqueue the item
        if (!$nodeId) {
            $this->requeueItem($item);
            $this->logError(sprintf(__('Retrying (%d) directory: %s', 'dup-challenge'), $item->getRetry(), $item->getPath()));
        }

        $item->setRecordId($nodeId);
        $this->enqueueChildDirectories($item);
    }

    /**
     * Enqueue child directories
     *
     * @param ScannerQueueItem $parentItem The parent item
     *
     * @return void
     */
    private function enqueueChildDirectories(ScannerQueueItem $parentItem)
    {
        $iterator = new RecursiveDirectoryIterator($parentItem->getPath(), RecursiveDirectoryIterator::SKIP_DOTS);

        foreach ($iterator as $child) {
            // Check if the child is readable
            if (!$child->isReadable()) {
                $this->logError(sprintf(__('Skipping unreadable file: %s', 'dup-challenge'), $child->getPathname()));
                continue;
            }

            // Get the ancestors of the parent item
            $ancestors = $parentItem->getAncestors();
            $ancestors[] = $parentItem;

            $this->queue->enqueue(
                new ScannerQueueItem(
                    $child->getPathname(),
					$child->getFilename(),
                    $child->getType(),
                    $ancestors,
                    $parentItem->getDepth() + 1,
                    $parentItem,
                    $child->getSize(),
                    $child->getMTime()
                )
            );
        }
    }

    /**
     * Process a file
     *
     * @param ScannerQueueItem $item The item to process
     *
     * @return void
     */
    private function processFile(ScannerQueueItem $item)
    {
        $nodeId = $this->insertNode($item);

        // If the node was not inserted, re-enqueue the item
        if (!$nodeId) {
            $this->requeueItem($item);
            $this->logError(sprintf(__('Retrying (%d) file: %s', 'dup-challenge'), $item->getRetry(), $item->getPath()));
        }
    }

    /**
     * Insert a node into the database
     *
     * @param ScannerQueueItem $item The item to insert
     *
     * @return int The inserted node ID
     */
    private function insertNode(ScannerQueueItem $item)
    {
        global $wpdb;

        // Start transaction
        $wpdb->query('START TRANSACTION');

        try {
            $data = [
                FileSystemNodesTable::COLUMN_PATH => $item->getPath(),
				FileSystemNodesTable::COLUMN_NAME => $item->getName(),
                FileSystemNodesTable::COLUMN_TYPE => $this->getNodeFileType($item),
                FileSystemNodesTable::COLUMN_NODE_COUNT => 1, // Node count for directories will be updated after the scan
                FileSystemNodesTable::COLUMN_SIZE => $item->isDir() ? 0 : $item->getSize(), // Size for directories will be updated after the scan
                FileSystemNodesTable::COLUMN_LAST_MODIFIED => $item->getLastModified(),
				FileSystemNodesTable::COLUMN_PARENT_ID => $item->getParent() ? $item->getParent()->getRecordId() : null
            ];


            $nodeId = $this->tableController->insertData(FileSystemNodesTable::getInstance()->getName(), $data);

            if (!$nodeId) {
                throw new Exception(__('Failed to insert node', 'dup-challenge'));
            }

            $item->setRecordId($nodeId);
            $this->insertClosureRecords($item);

            // Commit transaction if all inserts succeeded
            $wpdb->query('COMMIT');

            return $nodeId;
        } catch (Exception $e) {
            // Rollback the transaction
            $wpdb->query('ROLLBACK');
            $this->logError(sprintf(__('Insert node failed: ', 'dup-challenge'), $e->getMessage()));
            return 0;
        }
    }

    /**
     * Insert closure records into the database
     *
     * @param ScannerQueueItem $item The item to insert closure records for
     *
     * @throws Exception If the closure insertion fails
     *
     * @return void
     */
    private function insertClosureRecords(ScannerQueueItem $item)
    {
        $ancestors = $item->getAncestors();

        // If the current item is a directory, add a closure record for itself
        if ($item->isDir()) {
            $ancestors[] = $item;
        }

        foreach ($ancestors as $ancestor) {
            $ancestorId = $ancestor->getRecordId();
            $decendantId = $item->getRecordId();

            if ($ancestorId && $decendantId) {
                $inserted = $this->tableController->insertData(
                    FileSystemClosureTable::getInstance()->getName(),
                    [
						FileSystemClosureTable::COLUMN_ANCESTOR => $ancestorId,
						FileSystemClosureTable::COLUMN_DESCENDANT => $decendantId,
						FileSystemClosureTable::COLUMN_DEPTH => $item->getDepthRelativeTo($ancestor)
                    ]
                );

				if (!$inserted) {
					throw new Exception(sprintf(__('Failed to insert closure record for ancestor ID: %d and descendant ID: %d', 'dup-challenge'), $ancestorId, $decendantId));
				}
            }
        }
    }

    /**
     * Cleanup the database tables
     *
     * @return bool True if the cleanup was successful, false otherwise
     */
    private function cleanup()
    {
        $this->queue->resetState();

        // Truncate tables and return the final status
        $closureTableTruncate = $this->tableController->truncateTable(FileSystemClosureTable::getInstance()->getName());
        $nodesTableTruncate = $this->tableController->truncateTable(FileSystemNodesTable::getInstance()->getName());

        return $closureTableTruncate && $nodesTableTruncate;
    }

    /**
     * Finalize the scan
     *
     * @return void
     */
    private function finalizeScan()
    {
        $this->queue->resetState();
        delete_transient(ScanQueueController::TRANSIENT_NAME);
        wp_clear_scheduled_hook(self::EVENT_NAME);

        // Update the node count and size for directories
        $this->updateDirectoryNodeCountAndSize();

        do_action(self::ACTION_SCAN_COMPLETE);
    }

    /**
     * Get the node file type
     *
     * @param ScannerQueueItem $item The item to get the file type for
     *
     * @return string The file type
     */
    private function getNodeFileType(ScannerQueueItem $item)
    {
        $fileType = $item->getType();

        return $this->isValidFileType($fileType) ? $fileType : FileSystemNodesTable::FILE_TYPE_UNKNOWN;
    }

    /**
     * Requeue an item
     *
     * @param ScannerQueueItem $item The item to requeue
     *
     * @return void
     */
    private function requeueItem(ScannerQueueItem $item)
    {
        $item->decrementRetry();
        $this->queue->enqueue($item);
    }

    /**
     * Check if the file type is valid
     *
     * @param string $fileType The file type
     *
     * @return bool True if the file type is valid, false otherwise
     */
    private function isValidFileType($fileType)
    {
        return in_array($fileType, FileSystemNodesTable::getFileTypes(), true);
    }

    /**
     * Update the node count and size for directories
     *
     * @return void
     */
    private function updateDirectoryNodeCountAndSize()
    {
        global $wpdb;

        $nodesTable = FileSystemNodesTable::getInstance()->getName();
        $nodesClosureTable = FileSystemClosureTable::getInstance()->getName();

        $dirQuery = "SELECT node1.id, SUM(node2.node_count) AS node_count, SUM(node2.size) AS size
			FROM $nodesTable node1
			JOIN $nodesTable node2
			JOIN $nodesClosureTable closure
			ON node1.id = closure.ancestor AND node2.id = closure.descendant
			WHERE node1.type = %s AND node1.id != node2.id
			GROUP BY node1.id";

        $directories = $wpdb->get_results($wpdb->prepare($dirQuery, FileSystemNodesTable::FILE_TYPE_DIR));

		foreach ($directories as $directory) {
			$result = $wpdb->update(
				$nodesTable,
				[
					FileSystemNodesTable::COLUMN_NODE_COUNT => $directory->node_count,
					FileSystemNodesTable::COLUMN_SIZE => $directory->size
				],
				['id' => $directory->id],
				['%d', '%d'],
				['%d']
			);

			if ($result === false) {
				$this->logError(__('Failed to update directory node count and size for node ID: ' . $directory->id, 'dup-challenge'));
			}
		}
    }

    /**
     * Log an error
     *
     * @param string $message The error message
     *
     * @return void
     */
    private function logError($message)
    {
        error_log(sprintf(__('Error: %s', 'dup-challenge'), $message));
    }
}
