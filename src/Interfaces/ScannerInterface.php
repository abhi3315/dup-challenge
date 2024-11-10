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
     * @param string $rootPath The root path to start the scan
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
