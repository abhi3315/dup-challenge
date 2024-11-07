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
	 * @return string
	 */
	public function getName();

	/**
	 * Get table schema
	 * 
	 * @return array
	 */
	public function getSchema();

	/**
	 * Primary key column name
	 * 
	 * @return string
	 */
	public function getPrimaryKey();

	/**
	 * Foreign key column name
	 * 
	 * @return array
	 */
	public function getForeignKey();
}