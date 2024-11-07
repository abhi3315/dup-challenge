<?php

namespace DupChallenge\Controllers\Tables;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;

/**
 * Class to create custom table for file system closure.
 */
class FileSystemClosureTable implements TableInterface
{

	// Use trait to implement singleton pattern
	use SingletonTrait;

	/**
	 * Table column names
	 */
	const COLUMN_ANCESTOR = 'ancestor';
	const COLUMN_DESCENDANT = 'descendant';
	const COLUMN_DEPTH = 'depth';

	/**
	 * Table name
	 */
	const TABLE_NAME = 'dup_file_system_closure';

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
			self::COLUMN_ANCESTOR => 'INT UNSIGNED NOT NULL',
			self::COLUMN_DESCENDANT => 'INT UNSIGNED NOT NULL',
			self::COLUMN_DEPTH => 'INT UNSIGNED NOT NULL',
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getPrimaryKey()
	{
		return sprintf(
			'%1$s,%2$s',
			self::COLUMN_ANCESTOR,
			self::COLUMN_DESCENDANT
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getForeignKey()
	{
		return [
			self::COLUMN_ANCESTOR => $this->getAncestorForeignKey(),
			self::COLUMN_DESCENDANT => $this->getDescendantForeignKey(),
		];
	}

	/**
	 * Get ancestor foreign key
	 * 
	 * @return string
	 */
	private function getAncestorForeignKey()
	{
		return sprintf(
			'%1$s(%2$s)',
			FileSystemNodesTable::getInstance()->getName(),
			FileSystemNodesTable::COLUMN_ID
		);
	}

	/**
	 * Get descendant foreign key
	 *
	 * @return string
	 */
	private function getDescendantForeignKey()
	{
		return sprintf(
			'%1$s(%2$s)',
			FileSystemNodesTable::getInstance()->getName(),
			FileSystemNodesTable::COLUMN_ID
		);
	}
}