<?php

namespace DupChallenge\Utils;

/**
 * Scanner Queue Item
 */
class ScannerQueueItem
{
    /**
     * File path
     *
     * @var string
     */
    private $path;

    /**
     * File type
     *
     * @var string
     */
    private $type;

	/**
	 * File name
	 *
	 * @var string
	 */
	private $name;

    /**
     * Depth relative to the root path
     *
     * @var int
     */
    private $depth;

    /**
     * Immediate parent
     *
     * @var ScannerQueueItem
     */
    private $parent;

    /**
     * Size of the file
     *
     * @var int
     */
    private $size;

    /**
     * Last modified time
     *
     * @var int
     */
    private $lastModified;

    /**
     * Maximum number of retries
     *
     * @var int
     */
    private $retry = 3;

    /**
     * Ancestors
     *
     * @var ScannerQueueItem[]
     */
    private $ancestors;

    /**
     * Database record id
     *
     * @var int
     */
    private $recordId = -1;

    /**
     * Constructor
     *
     * @param string           $path         Path of the file or directory
	 * @param string           $name         Name of the file or directory
     * @param string           $type         File type
     * @param array            $ancestors    Ancestors of the current file or directory
     * @param int              $depth        Depth of the item relative to the root
     * @param ScannerQueueItem $parent       Immediate parent
     * @param int              $size         Size of the file
     * @param int              $lastModified Last modified time
     */
    public function __construct($path, $name, $type = 'dir', $ancestors = [], $depth = 0, $parent = null, $size = 0, $lastModified = 0)
    {
        $this->path = $path;
		$this->name = $name;
        $this->type = $type;
        $this->depth = $depth;
        $this->parent = $parent;
        $this->ancestors = $ancestors;
        $this->size = $size;
        $this->lastModified = $lastModified;
    }

    /**
     * Get the file path
     *
     * @return string Path of the file or directory
     */
    public function getPath()
    {
        return $this->path;
    }

	/**
	 * Get the file name
	 * 
	 * @return string Name of the file or directory
	 */
	public function getName()
	{
		return $this->name;
	}

    /**
     * Get the file type
     *
     * @return string File type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is a directory
     *
     * @return bool True if the item is a directory, false otherwise
     */
    public function isDir()
    {
        return $this->type === 'dir';
    }

    /**
     * Get the size of the file
     *
     * @return int Size of the file
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the last modified time
     *
     * @return int Last modified time
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Get the depth relative to the root path
     *
     * @return int Depth of the item relative to the root
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Get the depth relative to given item
     *
     * @param ScannerQueueItem $item The item to compare against
     *
     * @return int Depth of the item relative to the given item
     */
    public function getDepthRelativeTo(ScannerQueueItem $item)
    {
        return $this->depth - $item->getDepth();
    }

    /**
     * Get the immediate parent
     *
     * @return ScannerQueueItem Immediate parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the maximum number of retries
     *
     * @return int Maximum number of retries
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * Decrement the number of retries
     *
     * @return void
     */
    public function decrementRetry()
    {
        $this->retry--;
    }

    /**
     * Check if the item has retries
     *
     * @return bool True if the item has retries, false otherwise
     */
    public function hasRetries()
    {
        return $this->retry > 0;
    }

    /**
     * Get the set of ancestors
     *
     * @return ScannerQueueItem[] Ancestors of the current file or directory
     */
    public function getAncestors()
    {
        return $this->ancestors;
    }

    /**
     * Get the database record id
     *
     * @return int Database record id
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * Set the database record id
     *
     * @param int $recordId Database record id
     *
     * @return void
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;
    }
}
