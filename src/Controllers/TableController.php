<?php

namespace DupChallenge\Controllers;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;
use DupChallenge\Interfaces\TableControllerInterface;

/**
 * Singleton class controller to create custom table.
 */
class TableController implements TableControllerInterface
{
    use SingletonTrait;

    /**
     * @inheritDoc
     *
     * @param TableInterface $table The table to create
     *
     * @return bool True on success, false on failure
     */
    public function createTable(TableInterface $table = null)
    {
        if ($table === null) {
            return false; // Return false if no table is provided
        }

        // Global WordPress database object
        global $wpdb;

        // Get table SQL query and charset
        $tableName = $table->getName();
        $columnsSql = $this->getColumnSql($table);
        $keysSql = $this->getKeysSql($table);
        $foreignKeysSql = $this->getForeignKeysSql($table);
        $tableCharset = $wpdb->get_charset_collate();

        // Include upgrade.php for dbDelta function
        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Create table if it does not exist
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
			{$columnsSql}
			{$keysSql}
			{$foreignKeysSql}
		) {$tableCharset};";

        dbDelta($sql);

        return $this->tableExists($tableName);
    }

    /**
     * Returns SQL string for the columns in the given table schema
     *
     * @param TableInterface $table The table to get the columns from
     *
     * @return string SQL string for the columns
     */
    private function getColumnSql(TableInterface $table)
    {
        $schema = $table->getSchema();
        $sql = '';
        foreach ($schema as $name => $definition) {
            $sql .= "\n\t{$name} {$definition},";
        }

        return rtrim($sql, ',');
    }

    /**
     * Returns the SQL string for the keys of the given table
     *
     * @param TableInterface $table The table to get the keys from
     *
     * @return string SQL string for the keys
     */
    private function getKeysSql(TableInterface $table)
    {
        $primaryKey = $table->getPrimaryKey();
        return $primaryKey ? ",\n\tPRIMARY KEY  ({$primaryKey})" : '';
    }

    /**
     * Returns the SQL string for the foreign keys of the given table
     *
     * @param TableInterface $table The table to get the foreign keys from
     *
     * @return string SQL string for the foreign keys
     */
    private function getForeignKeysSql(TableInterface $table)
    {
        $foreignKeys = '';
        $foreignKeysArray = $table->getForeignKey();

        // Return empty string if no foreign keys
        if (empty($foreignKeysArray) || !is_array($foreignKeysArray)) {
            return $foreignKeys;
        }

        foreach ($foreignKeysArray as $column => $foreignKey) {
            $foreignKeys .= ",\n\tFOREIGN KEY ({$column}) REFERENCES {$foreignKey} ON DELETE CASCADE";
        }

        return "{$foreignKeys}\n";
    }

    /**
     * @inheritDoc
     *
     * @param string $tableName The name of the table to check
     *
     * @return bool True if the table exists, false otherwise
     */
    public function tableExists($tableName)
    {
        global $wpdb;

        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $tableName);

        if ($wpdb->get_var($query) === $tableName) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @param string $tableName The name of the table to drop
     *
     * @return bool True on success, false on failure
     */
    public function dropTable($tableName)
    {
        global $wpdb;

        $tableName = esc_sql($tableName);

        $query = "DROP TABLE IF EXISTS {$tableName}";

        if ($wpdb->query($query) === false) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     *
     * @param string               $tableName The name of the table to insert into
     * @param array<string, mixed> $data      An associative array of column names and values to insert
     *
     * @return false|int False on failure, the id of the inserted row on success
     */
    public function insertData($tableName, array $data)
    {
        global $wpdb;

        $wpdb->insert($tableName, $data);

        if ($wpdb->insert_id === 0) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * @inheritDoc
     *
     * @param string               $tableName The name of the table to delete from
     * @param array<string, mixed> $where     An associative array of column names and values to match
     *
     * @return int|false The number of rows affected, or false on failure
     */
    public function deleteData($tableName, array $where)
    {
        global $wpdb;

        return $wpdb->delete($tableName, $where);
    }

    /**
     * @inheritDoc
     *
     * @param string $tableName The name of the table to truncate
     *
     * @return bool True on success, false on failure
     */
    public function truncateTable($tableName)
    {
        global $wpdb;

        $tableName = esc_sql($tableName);

        // Disable foreign key checks
        $wpdb->query('SET foreign_key_checks = 0');

        $query = "TRUNCATE TABLE {$tableName}";

        $result = $wpdb->query($query);

        // Re-enable foreign key checks
        $wpdb->query('SET foreign_key_checks = 1');

        return boolval($result);
    }

    /**
     * Get count of rows in a table
     *
     * @param string $tableName The name of the table to count
     *
     * @return int The number of rows in the table
     */
    public function getRowCount($tableName)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT COUNT(*) FROM {$tableName}");

        return intval($wpdb->get_var($query));
    }
}
