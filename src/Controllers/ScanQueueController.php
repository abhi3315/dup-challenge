<?php

namespace DupChallenge\Controllers;

use SplQueue;
use DupChallenge\Utils\ScannerQueueItem;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\QueueInterface;

/**
 * Scan queue controller
 */
class ScanQueueController implements QueueInterface
{
    use SingletonTrait;

    const TRANSIENT_NAME = 'dup_challenge_scan_queue';

    /**
     * Queue
     * 
     * @var SplQueue
     */
    private $queue;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->loadState();
    }

    /**
     * @inheritDoc
     * 
     * @param ScannerQueueItem $item
     */
    public function enqueue($item)
    {
        $this->queue->enqueue($item);
    }

    /**
     * @inheritDoc
     * 
     * @return ScannerQueueItem|null Queue item
     */
    public function dequeue()
    {
        return !$this->queue->isEmpty() ? $this->queue->dequeue() : null;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return $this->queue->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function saveState()
    {
        set_transient(self::TRANSIENT_NAME, $this->queue);
    }

    /**
     * @inheritDoc
     */
    public function loadState()
    {
        $this->queue = get_transient(self::TRANSIENT_NAME);

        if (!$this->queue instanceof SplQueue) {
            $this->queue = new SplQueue();
        }
    }

    /**
     * @inheritDoc
     */
    public function resetState()
    {
        $this->queue = new SplQueue();
        $this->saveState();
    }
}