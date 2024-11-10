<?php

namespace DupChallenge\Controllers\Tables;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;

/**
 * Class to create custom table for file system nodes.
 */
class FileSystemNodesTable implements TableInterface
{
	use SingletonTrait;

	/**
	 * Table column names
	 */
	const COLUMN_ID = 'id';
	const COLUMN_PATH = 'path';
	const COLUMN_TYPE = 'type';
	const COLUMN_SIZE = 'size';
	const COLUMN_NODE_COUNT = 'node_count';
	const COLUMN_LAST_MODIFIED = 'last_modified';
	const COLUMN_LAST_SCANNED = 'last_scanned';

	/**
	 * Table name
	 */
	const TABLE_NAME = 'dup_file_system_nodes';

	/**
     * File types
     */
    const FILE_TYPE_FILE = 'file';
    const FILE_TYPE_DIR = 'dir';
    const FILE_TYPE_LINK = 'link';
    const FILE_TYPE_BLOCK = 'block';
    const FILE_TYPE_FIFO = 'fifo';
    const FILE_TYPE_CHAR = 'char';
    const FILE_TYPE_SOCKET = 'socket';
    const FILE_TYPE_UNKNOWN = 'unknown';

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		global $wpdb;

		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * @inheritDoc
	 */
	public function getSchema()
	{
		return [
			self::COLUMN_ID => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			self::COLUMN_PATH => 'VARCHAR(255) NOT NULL UNIQUE',
			self::COLUMN_TYPE => 'ENUM(\'' . implode("','", self::getFileTypes()) . '\') DEFAULT \'' . self::FILE_TYPE_UNKNOWN . '\'',
			self::COLUMN_SIZE => 'BIGINT UNSIGNED DEFAULT 0',
			self::COLUMN_NODE_COUNT => 'INT UNSIGNED DEFAULT 1',
			self::COLUMN_LAST_MODIFIED => 'DATETIME DEFAULT NULL',
			self::COLUMN_LAST_SCANNED => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getPrimaryKey()
	{
		return self::COLUMN_ID;
	}

	/**
	 * @inheritDoc
	 */
	public function getForeignKey()
	{
		return null; // No foreign keys for this table
	}

	/**
     * Retrieve the available file types.
     *
     * @return array The available file types
     */
    private static function getFileTypes(): array
    {
        return [
            self::FILE_TYPE_FILE,
            self::FILE_TYPE_DIR,
            self::FILE_TYPE_LINK,
            self::FILE_TYPE_BLOCK,
            self::FILE_TYPE_FIFO,
            self::FILE_TYPE_CHAR,
            self::FILE_TYPE_SOCKET,
            self::FILE_TYPE_UNKNOWN,
        ];
    }
}