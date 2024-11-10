<?php

namespace DupChallenge\Interfaces;

/**
 * Scanner interface
 */
interface ScannerInterface
{
    /**
     * Start the scan process
     * 
     * @param string $rootPath
     * 
     * @return void
     */
    public function startScanJob(string $rootPath);

    /**
     * Process the scan chunk
     * 
     * @return void
     */
    public function processScanChunk();
}