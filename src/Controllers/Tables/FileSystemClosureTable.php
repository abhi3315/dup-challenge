<?php

namespace DupChallenge\Controllers\Tables;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;

/**
 * Class to create custom table for file system closure.
 */
class FileSystemClosureTable implements TableInterface
{
    use SingletonTrait;

    /**
     * Table column names
     */
    const COLUMN_ID = 'id';
    const COLUMN_ANCESTOR = 'ancestor';
    const COLUMN_DESCENDANT = 'descendant';
    const COLUMN_DEPTH = 'depth';

    /**
     * Table name
     */
    const TABLE_NAME = 'dup_file_system_closure';

    /**
     * @inheritDoc
     *
     * @return string The name of the table
     */
    public function getName()
    {
        global $wpdb;

        return $wpdb->prefix . self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     *
     * @return array The schema of the table
     */
    public function getSchema()
    {
        return [
            self::COLUMN_ID => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
            self::COLUMN_ANCESTOR => 'INT UNSIGNED NOT NULL',
            self::COLUMN_DESCENDANT => 'INT UNSIGNED NOT NULL',
            self::COLUMN_DEPTH => 'INT UNSIGNED NOT NULL',
        ];
    }

    /**
     * @inheritDoc
     *
     * @return string The primary key of the table
     */
    public function getPrimaryKey()
    {
        return self::COLUMN_ID;
    }

    /**
     * @inheritDoc
     *
     * @return array The foreign keys of the table
     */
    public function getForeignKey()
    {
        return [
            self::COLUMN_ANCESTOR => $this->getForeignKeyDefinition(),
            self::COLUMN_DESCENDANT => $this->getForeignKeyDefinition(),
        ];
    }

    /**
     * Get foreign key definition for ancestor or descendant
     *
     * @return string The foreign key definition
     */
    private function getForeignKeyDefinition()
    {
        return sprintf(
            '%1$s(%2$s)',
            FileSystemNodesTable::getInstance()->getName(),
            FileSystemNodesTable::COLUMN_ID
        );
    }
}
