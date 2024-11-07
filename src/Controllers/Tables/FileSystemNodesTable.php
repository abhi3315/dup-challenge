<?php

namespace DupChallenge\Controllers\Tables;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;

/**
 * Class to create custom table for file system nodes.
 */
class FileSystemNodesTable implements TableInterface
{

	// Use trait to implement singleton pattern
	use SingletonTrait;

	/**
	 * Table column names
	 */
	const COLUMN_ID = 'id';
	const COLUMN_PATH = 'path';
	const COLUMN_TYPE = 'type';
	const COLUMN_SIZE = 'size';
	const COLUMN_NODE_COUNT = 'node_count';
	const COLUMN_LAST_SCANNED = 'last_scanned';

	/**
	 * Table name
	 */
	const TABLE_NAME = 'dup_file_system_nodes';

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
			self::COLUMN_PATH => 'VARCHAR(255) NOT NULL',
			self::COLUMN_TYPE => 'ENUM("file", "dir") NOT NULL',
			self::COLUMN_SIZE => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
			self::COLUMN_NODE_COUNT => 'INT UNSIGNED DEFAULT 1',
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
		return null;
	}
}