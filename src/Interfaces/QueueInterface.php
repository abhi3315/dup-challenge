<?php

namespace DupChallenge\Interfaces;

/**
 * Queue interface
 */
interface QueueInterface
{
	/**
	 * Enqueue an item
	 * 
	 * @param array $item
	 * 
	 * @return void
	 */
	public function enqueue($item);

	/**
	 * Dequeue an item
	 * 
	 * @return array
	 */
	public function dequeue();

	/**
	 * Is the queue empty
	 * 
	 * @return bool
	 */
	public function isEmpty();

	/**
	 * Save the queue state
	 * 
	 * @return void
	 */
	public function saveState();

	/**
	 * Load the queue state
	 * 
	 * @return void
	 */
	public function loadState();
}