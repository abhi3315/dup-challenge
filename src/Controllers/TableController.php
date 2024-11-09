<?php

namespace DupChallenge\Controllers;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;
use DupChallenge\Interfaces\BaseControllerInterface;

/**
 * Singleton class controller to create custom table.
 */
class TableController implements BaseControllerInterface
{

	// Use trait to implement singleton pattern
	use SingletonTrait;

    /**
     * Initialize the scanner
	 * 
	 * @param TableInterface $table
     *
     * @return bool
     */
    public function createTable(TableInterface $table = null)
	{
		if ($table === null) {
			return;
		}

		// Global WordPress database object
		global $wpdb;

		// Get table SQL query and charset
		$tableName = $table->getName();
		$columnsSql = $this->getColumnSql($table);
		$keysSql = $this->getKeysSql($table);
		$foreignKeysSql = $this->getForeignKeysSql($table);
		$tableCharset = $wpdb->get_charset_collate();

		/**
         * WordPress file with the dbDelta() function.
         */
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta("CREATE TABLE IF NOT EXISTS {$tableName} (
			{$columnsSql}
			{$keysSql}
			{$foreignKeysSql}
		) {$tableCharset};");

		// Check if table was created
		if (!$this->tableExists($tableName)) {
			return false;
		}

		return true;
	}

	/**
     * Returns SQL string for the columns in the given table schema
     *
     * @param TableInterface $table
     * @return string
     */
    private function getColumnSql(TableInterface $table)
	{
		$schema = $table->getSchema();
		$sql = '';
		foreach ($schema as $name => $definition) {
			$sql and $sql .= ',';
			$sql .= "\n\t{$name} {$definition}";
		}

		return $sql;
	}

	/**
     * Returns the SQL string for the keys of the given table
     *
     * @param TableInterface $table
     * @return string
     */
    private function getKeysSql(TableInterface $table)
    {
        $keys = '';
        $primaryKey = $table->getPrimaryKey();
        if ($primaryKey) {
            // Due to dbDelta: two spaces after PRIMARY KEY!
            $keys .= ",\n\tPRIMARY KEY  ({$primaryKey})";
        }

        return "{$keys}\n";
    }

	/**
	 * Returns the SQL string for the foreign keys of the given table
	 *
	 * @param TableInterface $table
	 * @return string
	 */
	private function getForeignKeysSql(TableInterface $table)
	{
		$foreignKeys = '';
		$foreignKeysArray = $table->getForeignKey();

		if (empty($foreignKeysArray) || !is_array($foreignKeysArray)) {
			return $foreignKeys;
		}

		foreach ($foreignKeysArray as $column => $foreignKey) {
			$foreignKeys .= ",\n\tFOREIGN KEY ({$column}) REFERENCES {$foreignKey}";
		}

		return "{$foreignKeys}\n";
	}

	/**
	 * Check if a table exists
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	private function tableExists($tableName)
	{
		global $wpdb;

		$query = $wpdb->prepare("SHOW TABLES LIKE %s", $tableName);

		if ($wpdb->get_var($query) === $tableName) {
			return true;
		}

		return false;
	}

	/**
	 * Drop table
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	public function dropTable($tableName)
	{
		global $wpdb;

		$query = $wpdb->prepare("DROP TABLE IF EXISTS %s", $tableName);

		if ($wpdb->query($query) === false) {
			return false;
		}

		return true;
	}

	/**
	 * Insert data into table
	 * 
	 * @param string $tableName
	 * @param array $data
	 * 
	 * @return bool|int
	 */
	public function insertData($tableName, $data)
	{
		global $wpdb;

		$wpdb->insert($tableName, $data);

		if ($wpdb->insert_id === 0) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Truncate table
	 * 
	 * @param string $tableName
	 * 
	 * @return bool
	 */
	public function truncateTable($tableName)
	{
		global $wpdb;

		$query = $wpdb->prepare("TRUNCATE TABLE %s", $tableName);

		if ($wpdb->query($query) === false) {
			return false;
		}

		return true;
	}
}
