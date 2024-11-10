<?php

namespace DupChallenge\Controllers;

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
	protected function __construct( QueueInterface $queue, TableControllerInterface $tableController, int $chunkSize = DUP_CHUNK_SIZE, int $chunkProcessingGap = DUP_CHUNK_PROCESSING_GAP)
	{
		$this->queue = $queue;
		$this->tableController = $tableController;
		$this->chunkSize = $chunkSize;
		$this->chunkProcessingGap = $chunkProcessingGap;
	}

	/**
	 * @inheritDoc
	 */
	public function startScanJob( string $rootPath = DUP_WP_ROOT_PATH)
	{
		if (!is_dir($rootPath)) {
			return;
		}

		$this->cleanup();
		$this->queue->enqueue( new ScannerQueueItem($rootPath) );
		$this->queue->saveState();

		if(!wp_next_scheduled(self::EVENT_NAME)) {
			wp_schedule_single_event( time() + $this->chunkProcessingGap, self::EVENT_NAME);
		}

		do_action(self::ACTION_SCAN_START);
	}

    public function processScanChunk(): void
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

		if (!$this->queue->isEmpty()) {
			wp_schedule_single_event(time() + $this->chunkProcessingGap, self::EVENT_NAME);
		}
    }

	/**
	 * Check if an item is processable
	 * 
	 * @param ScannerQueueItem $item
	 */
	private function isProcessable(?ScannerQueueItem $item): bool
	{
		return $item instanceof ScannerQueueItem && $item->hasRetries();
	}

	/**
	 * Process an item
	 * 
	 * @param ScannerQueueItem $item
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
	 * @param ScannerQueueItem $item
	 * 
	 * @return void
	 */
	private function processDirectory(ScannerQueueItem $item)
	{
		$nodeId = $this->insertNode($item);

		// If the node was not inserted, re-enqueue the item
		if (!$nodeId) {
			$item->decrementRetry();
			$this->queue->enqueue($item);
			error_log(sprintf(__('(%d) Retrying directory: %s', 'dup-challenge'), $item->getRetry(), $item->getPath()));
		}

		$item->setRecordId($nodeId);
		$this->enqueueChildDirectories($item);
	}

	/**
	 * Enqueue child directories
	 * 
	 * @param ScannerQueueItem $parentItem
	 * 
	 * @return void
	 */
	private function enqueueChildDirectories(ScannerQueueItem $parentItem)
	{
		$iterator = new RecursiveDirectoryIterator($parentItem->getPath(), RecursiveDirectoryIterator::SKIP_DOTS);

		foreach ($iterator as $child) {
			// Check if the child is readable
			if (!$child->isReadable()) {
				error_log(sprintf(__('Skipping unreadable file: %s', 'dup-challenge'), $child->getPathname()));
				continue;
			}

			$this->queue->enqueue(
				new ScannerQueueItem(
					$child->getPathname(),
					$child->getType(),
					[...$parentItem->getAncestors(), $parentItem],
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
	 * @param ScannerQueueItem $item
	 * 
	 * @return void
	 */
	private function processFile(ScannerQueueItem $item)
	{
		$nodeId = $this->insertNode($item);

		// If the node was not inserted, re-enqueue the item
		if (!$nodeId) {
			$item->decrementRetry();
			$this->queue->enqueue($item);
			error_log(sprintf(__('(%d) Retrying file: %s', 'dup-challenge'), $item->getRetry(), $item->getPath()));
		}
	}

	/**
	 * Insert a node into the database
	 * 
	 * @param ScannerQueueItem $item
	 * 
	 * @return int
	 */
	private function insertNode(ScannerQueueItem $item)
	{
		global $wpdb;

		// Start transaction
		$wpdb->query('START TRANSACTION');

		try {
			$data = [
				FileSystemNodesTable::COLUMN_PATH => $item->getPath(),
				FileSystemNodesTable::COLUMN_TYPE => $item->getType(),
				FileSystemNodesTable::COLUMN_NODE_COUNT =>$item->isDir() ? 0 : 1, // Node count for directories will be updated after the scan
				FileSystemNodesTable::COLUMN_SIZE => $item->isDir() ? 0 : $item->getSize(), // Size for directories will be updated after the scan
				FileSystemNodesTable::COLUMN_LAST_MODIFIED => $item->getLastModified()
			];

			
			$nodeId = $this->tableController->insertData(FileSystemNodesTable::getInstance()->getName(),$data);
			
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
			error_log(sprintf(__('Insert node failed: ', 'dup-challenge'), $e->getMessage()));
			return 0;
		}
	}

	/**
	 * Insert closure records into the database
	 * 
	 * @param ScannerQueueItem $item
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

			if($ancestorId && $decendantId) {
				$this->tableController->insertData(FileSystemClosureTable::getInstance()->getName(), [
					FileSystemClosureTable::COLUMN_ANCESTOR => $ancestorId,
					FileSystemClosureTable::COLUMN_DESCENDANT => $decendantId,
					FileSystemClosureTable::COLUMN_DEPTH => $item->getDepthRelativeTo($ancestor)
				]);
			}
		}
	}

	/**
	 * Cleanup the database tables
	 * 
	 * @return void
	 */
	private function cleanup()
	{
		$this->queue->resetState();
		$this->tableController->truncateTable(FileSystemClosureTable::getInstance()->getName());
		$this->tableController->truncateTable(FileSystemNodesTable::getInstance()->getName());
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
		do_action(self::ACTION_SCAN_COMPLETE);
	}
}