<?php

namespace DupChallenge\Controllers;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Singleton class controller for scanner.
 * 
 * RecursiveIteratorIterator with RecursiveDirectoryIterator to scan the directory
 */
class ScannerController extends AbstractController
{

    /**
     * Class constructor
     */
    protected function __construct()
    {
		// Initialize the scanner
		$this->init();
    }

	/**
	 * Initialize the scanner
	 *
	 * @return void
	 */
	private function init()
	{
		// Scan the directory
		$this->scanDirectory(DUP_WP_ROOT_PATH);
	}

	/**
	 * Scan the directory
	 *
	 * @param string $directory
	 * @return void
	 */
	private function scanDirectory($directory)
	{
		// Check if the directory exists
		if (!is_dir($directory)) {
			return;
		}

		// Scan the directory
		$directory = new RecursiveDirectoryIterator($directory);
		$iterator = new RecursiveIteratorIterator($directory);

		// List all the files in the directory
		foreach ($iterator as $file) {
			// Check if the file is a PHP file
			if ($file->isFile() && $file->getExtension() === 'php') {
				// Check if the file is readable
				if ($file->isReadable()) {
					// Read the file
					$this->readFile($file->getPathname());
				}
			}
		}

		die;
	}

	/**
	 * Read the file
	 *
	 * @param string $file
	 * @return void
	 */
	private function readFile($file)
	{
		// Read the file
		$contents = file_get_contents($file);

		// Check if the file contains the plugin header
		if (strpos($contents, 'Duplicator Challenge Plugin') !== false) {
			// Display the file path
			echo $file . PHP_EOL;
		}
	}
}
