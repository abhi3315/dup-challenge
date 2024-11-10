<?php

namespace DupChallenge\Interfaces;

/**
 * Table controller interface
 */
interface TableControllerInterface
{
	/**
	 * Create a table
	 * 
	 * @param TableInterface $table
	 * 
	 * @return bool
	 */
	public function createTable(TableInterface $table);

	/**
	 * Check if a table exists
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	public function tableExists($tableName);

	/**
	 * Drop a table
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	public function dropTable($tableName);

	/**
	 * Insert a row into a table
	 * Returns the id of the inserted row or false on failure
	 * 
	 * @param string $tableName
	 * @param array $data
	 *
	 * @return false|int
	 */
	public function insertData($tableName, $data);

	/**
	 * Delete a row from a table
	 * 
	 * @param string $tableName
	 * 
	 * @return int|false
	 */
	public function deleteData($tableName, $where);


	/**
	 * Truncate a table
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	public function truncateTable($tableName);
}