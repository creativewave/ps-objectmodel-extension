<?php

namespace CW\ObjectModel;

use CW\Db\Table\Definition\Model;
use CW\Db\Table\Definition\Relation;
use CW\Db\Table\Schema;

interface DefinitionInterface
{
    /**
     * Get key value from \ObjectModel::$definition.
     */
    public function get(string $key);

    /**
     * Get tables schemas.
     */
    public function getSchemas(): array;

    /**
     * Get table schema.
     */
    public function getSchema(string $id): Schema;

    /**
     * Get model definition.
     */
    public function getModel(): Model;

    /**
     * Get relations definitions.
     */
    public function getRelations(array $ids): array;

    /**
     * Get relation definition.
     */
    public function getRelation(string $id): Relation;

    /**
     *  Get tables IDs to create.
     */
    public function getIds(): array;

    /**
     * Get model table ID.
     */
    public function getIdModel(): string;

    /**
     * Get relations tables IDs.
     */
    public function getIdsRelations(int $type = null): array;

    /**
     * Get "ManyToMany" (aka. multi) relations tables IDs.
     */
    public function getIdsMultiRelations(): array;

    /**
     * Get "OneToMany" and "OneToOne" (aka single) relations tables IDs.
     */
    public function getIdsSingleRelations(): array;

    /**
     * Get tables names.
     */
    public function getNames(): array;

    /**
     * Get table name.
     */
    public function getName(string $id): string;

    /**
     * Get table engine.
     */
    public function getEngine(string $id): string;

    /**
     * Get table character set.
     */
    public function getCharset(string $id): string;

    /**
     * Get table collation.
     */
    public function getCollation(string $id): string;

    /**
     * Get table columns.
     */
    public function getColumns(string $id): array;

    /**
     * Get table columns from fields.
     */
    public function getColumnsFromFields(array $fields): array;

    /**
     * Get table column from field.
     */
    public function getColumnFromField(string $name, array $constraints): string;

    /**
     * Get table primary key.
     */
    public function getKeyPrimary(string $id): array;

    /**
     * Get table foreign keys.
     */
    public function getKeysForeign(string $id): array;

    /**
     * Get table (simple) keys.
     */
    public function getKeysSimple(string $id): array;

    /**
     * Get table unique keys.
     */
    public function getKeysUnique(string $id): array;

    /**
     * Get table fulltext keys.
     */
    public function getKeysFulltext(string $id): array;

    /**
     * Wether or not a table field is a (simple) key field.
     */
    public function isFieldSimpleKey(array $field): bool;

    /**
     * Wether or not a table field is a unique key field.
     */
    public function isFieldUniqueKey(array $field): bool;

    /**
     * Wether or not a table field is a fulltext key field.
     */
    public function isFieldFulltextKey(array $field): bool;
}
