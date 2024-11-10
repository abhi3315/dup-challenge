<?php

namespace DupChallenge\Interfaces;

/**
 * Table interface
 */
interface TableInterface
{
	/**
	 * Get table name
	 *
	 * @return string Table name
	 */
	public function getName();

	/**
	 * Get table schema
	 * 
	 * @return array Table schema
	 */
	public function getSchema();

	/**
	 * Primary key column name
	 * 
	 * @return string Primary key column name
	 */
	public function getPrimaryKey();

	/**
	 * Foreign key column name
	 * 
	 * @return array Foreign key column name
	 */
	public function getForeignKey();
}