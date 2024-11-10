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
     * @return bool True on success, false on failure
     */
    public function createTable(TableInterface $table);

    /**
     * Check if a table exists
     * 
     * @param string $tableName
     * 
     * @return bool True if the table exists, false otherwise
     */
    public function tableExists($tableName);

    /**
     * Drop a table
     * 
     * @param string $tableName
     * 
     * @return bool True on success, false on failure
     */
    public function dropTable($tableName);

    /**
     * Insert a row into a table
     * Returns the id of the inserted row or false on failure
     * 
     * @param string $tableName
     * @param array  $data
     *
     * @return false|int False on failure, the id of the inserted row on success
     */
    public function insertData($tableName, $data);

    /**
     * Delete a row from a table
     * 
     * @param string $tableName
     * 
     * @return int|false The number of rows affected, or false on failure
     */
    public function deleteData($tableName, $where);


    /**
     * Truncate a table
     * 
     * @param string $tableName
     * 
     * @return bool True on success, false on failure
     */
    public function truncateTable($tableName);
}