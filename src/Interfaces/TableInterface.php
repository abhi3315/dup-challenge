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
     * @return array<string, string> Table schema
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
     * @return array<string, string> Foreign key column name
     */
    public function getForeignKey();
}
