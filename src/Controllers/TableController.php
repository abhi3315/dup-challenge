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

		/**
         * WordPress file with the dbDelta() function.
         */
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

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
     * @param TableInterface $table
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
     * @param TableInterface $table
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
	 * @param TableInterface $table
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
	 * @inheritDoc
	 */
	public function deleteData($tableName, $where)
	{
		global $wpdb;

		return $wpdb->delete($tableName, $where);
	}

	/**
	 * @inheritDoc
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
}
