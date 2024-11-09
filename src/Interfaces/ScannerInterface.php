<?php

namespace DupChallenge\Interfaces;

/**
 * Scanner interface
 */
interface ScannerInterface
{
	/**
	 * Start the scan
	 * 
	 * @param string $rootDir
	 * 
	 * @return void
	 */
	public function startScanJob($rootDir);

	/**
	 * Process the scan chunk
	 * 
	 * @return void
	 */
	public function processScanChunk();
}