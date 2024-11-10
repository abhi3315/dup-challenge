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
	 * @param mixed $item
	 * 
	 * @return void
	 */
	public function enqueue($item);

	/**
	 * Dequeue an item
	 * 
	 * @return mixed Queue item
	 */
	public function dequeue();

	/**
	 * Is the queue empty
	 * 
	 * @return bool True if the queue is empty, false otherwise
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

	/**
	 * Reset the queue state
	 * 
	 * @return void
	 */
	public function resetState();
}