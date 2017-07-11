<?php

namespace CW\Db\Table\Definition;

interface DefinitionInterface
{
    /**
     * Defaults.
     */
    const CHARSET   = 'utf8mb4';
    const COLLATION = 'DEFAULT';
    const ENGINE    = _MYSQL_ENGINE_;
    const DB_PREFIX = _DB_PREFIX_;

    /**
     * Primary key field description.
     */
    const PRIMARY_KEY_FIELD = [
        'type'     => \ObjectModel::TYPE_INT,
        'validate' => 'isUnsignedId',
        'required' => true,
        'primary'  => true,
    ];

    /**
     * (Simple) Key field description.
     */
    const KEY_FIELD = [
        'type'     => \ObjectModel::TYPE_INT,
        'validate' => 'isUnsignedId',
        'required' => true,
    ];

    /**
     * Get name.
     */
    public function getName(): string;

    /**
     * Get primary column name.
     */
    public function getPrimary(): string;

    /**
     * Get engine.
     */
    public function getEngine(): string;

    /**
     * Get charset.
     */
    public function getCharset(): string;

    /**
     * Get collation.
     */
    public function getCollation(): string;

    /**
     * Get columns.
     */
    public function getColumns(): array;

    /**
     * Get foreign keys.
     */
    public function getKeysForeign(): array;

    /**
     * Get fulltext keys.
     */
    public function getKeysFulltext(): array;

    /**
     * Get primary key.
     */
    public function getKeyPrimary(): array;

    /**
     * Get (simple) keys.
     */
    public function getKeysSimple(): array;

    /**
     * Get unique keys.
     */
    public function getKeysUnique(): array;
}
