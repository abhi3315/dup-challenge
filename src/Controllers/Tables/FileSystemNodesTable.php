<?php

namespace DupChallenge\Controllers\Tables;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\TableInterface;

/**
 * Class to create custom table for file system nodes.
 */
class FileSystemNodesTable implements TableInterface
{
    use SingletonTrait;

    /**
     * Table column names
     */
    const COLUMN_ID = 'id';
    const COLUMN_PATH = 'path';
    const COLUMN_NAME = 'name';
    const COLUMN_TYPE = 'type';
    const COLUMN_SIZE = 'size';
    const COLUMN_PARENT_ID = 'parent_id';
    const COLUMN_NODE_COUNT = 'node_count';
    const COLUMN_LAST_MODIFIED = 'last_modified';
    const COLUMN_LAST_SCANNED = 'last_scanned';

    /**
     * Table name
     */
    const TABLE_NAME = 'dup_file_system_nodes';

    /**
        * File types
        */
    const FILE_TYPE_FILE = 'file';
    const FILE_TYPE_DIR = 'dir';
    const FILE_TYPE_LINK = 'link';
    const FILE_TYPE_BLOCK = 'block';
    const FILE_TYPE_FIFO = 'fifo';
    const FILE_TYPE_CHAR = 'char';
    const FILE_TYPE_SOCKET = 'socket';
    const FILE_TYPE_UNKNOWN = 'unknown';

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
     * @return array<string, string> The schema of the table
     */
    public function getSchema()
    {
        return [
            self::COLUMN_ID            => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
            self::COLUMN_PATH          => 'VARCHAR(255) NOT NULL UNIQUE',
            self::COLUMN_NAME          => 'VARCHAR(255) NOT NULL',
            self::COLUMN_TYPE          => 'ENUM(\'' . implode("','", self::getFileTypes()) . '\') DEFAULT \'' . self::FILE_TYPE_UNKNOWN . '\'',
            self::COLUMN_SIZE          => 'BIGINT UNSIGNED DEFAULT 0',
            self::COLUMN_PARENT_ID     => 'INT UNSIGNED DEFAULT NULL',
            self::COLUMN_NODE_COUNT    => 'INT UNSIGNED DEFAULT 1',
            self::COLUMN_LAST_MODIFIED => 'DATETIME DEFAULT NULL',
            self::COLUMN_LAST_SCANNED  => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
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
     * @return array<string, string> The foreign keys of the table
     */
    public function getForeignKey()
    {
        return [
            self::COLUMN_PARENT_ID => sprintf(
                '%1$s(%2$s)',
                $this->getName(),
                self::COLUMN_ID
            ),
        ];
    }

    /**
     * Retrieve the available file types.
     *
     * @return array<string> The available file types
     */
    public static function getFileTypes()
    {
        return [
            self::FILE_TYPE_FILE,
            self::FILE_TYPE_DIR,
            self::FILE_TYPE_LINK,
            self::FILE_TYPE_BLOCK,
            self::FILE_TYPE_FIFO,
            self::FILE_TYPE_CHAR,
            self::FILE_TYPE_SOCKET,
            self::FILE_TYPE_UNKNOWN,
        ];
    }
}
