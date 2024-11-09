<?php

namespace DupChallenge\Utils;

/**
 * Scanner Queue Item
 */
class QueueItem
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
	 * Depth relative to the root path
	 * 
	 * @var int
	 */
	private $depth;

	/**
	 * Immediate parent
	 * 
	 * @var QueueItem
	 */
	private $parent;

	/**
	 * Maximum number of retries
	 * 
	 * @var int
	 */
	private $retry;

	/**
	 * Ancestors
	 * 
	 * @var array
	 */
	private $ancestors;

	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @param string $type
	 * @param int $depth
	 * @param QueueItem $parent
	 * @param int $retry
	 * @param array $ancestors
	 */
	public function __construct($path, $type='dir', $depth=0, $parent=null, $retry=3, $ancestors=[])
	{
		$this->path = $path;
		$this->type = $type;
		$this->depth = $depth;
		$this->parent = $parent;
		$this->retry = $retry;
		$this->ancestors = $ancestors;
	}

	/**
	 * Get the file path
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Get the file type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Is a directory
	 * 
	 * @return bool
	 */
	public function isDir()
	{
		return $this->type === 'dir';
	}

	/**
	 * Get the depth relative to the root path
	 * 
	 * @return int
	 */
	public function getDepth()
	{
		return $this->depth;
	}

	/**
	 * Get the depth relative to given item
	 * 
	 * @param QueueItem $item
	 *
	 * @return int
	 */
	public function getDepthRelativeTo(QueueItem $item)
	{
		return $this->depth - $item->getDepth();
	}

	/**
	 * Get the immediate parent
	 * 
	 * @return QueueItem
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Get the maximum number of retries
	 * 
	 * @return int
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
	 * @return bool
	 */
	public function hasRetries()
	{
		return $this->retry > 0;
	}

	/**
	 * Get the ancestors
	 * 
	 * @return array
	 */
	public function getAncestors()
	{
		return $this->ancestors;
	}
}