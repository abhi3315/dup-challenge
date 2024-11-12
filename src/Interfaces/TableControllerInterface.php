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
     * @param TableInterface $table The table to create
     *
     * @return bool True on success, false on failure
     */
    public function createTable(TableInterface $table);

    /**
     * Check if a table exists
     *
     * @param string $tableName The name of the table to check
     *
     * @return bool True if the table exists, false otherwise
     */
    public function tableExists($tableName);

    /**
     * Drop a table
     *
     * @param string $tableName The name of the table to drop
     *
     * @return bool True on success, false on failure
     */
    public function dropTable($tableName);

    /**
     * Insert a row into a table
     * Returns the id of the inserted row or false on failure
     *
     * @param string               $tableName The name of the table to insert into
     * @param array<string, mixed> $data      An associative array of column names and values
     *
     * @return false|int False on failure, the id of the inserted row on success
     */
    public function insertData($tableName, array $data);

    /**
     * Delete a row from a table
     *
     * @param string               $tableName The name of the table to delete from
     * @param array<string, mixed> $where     An associative array of column names and values to match
     *
     * @return int|false The number of rows affected, or false on failure
     */
    public function deleteData($tableName, array $where);

    /**
     * Truncate a table
     *
     * @param string $tableName The name of the table to truncate
     *
     * @return bool True on success, false on failure
     */
    public function truncateTable($tableName);
}
