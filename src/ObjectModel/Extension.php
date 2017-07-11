<?php

namespace CW\ObjectModel;

use CW\Db\Table\Schema;
use CW\Db\Table\Table;

class Extension implements ExtensionInterface
{
    /**
     * @var \ObjectModel
     */
    protected $om;

    /**
     * @var \Db
     */
    protected $db;

    /**
     * Register \ObjectModel and \Db.
     */
    public function __construct(\ObjectModel $om, \Db $db)
    {
        $this->om = $om;
        $this->db = $db;
    }

    /**
     * @see \CW\ObjectModel\ExtensionInterface
     */
    public function install(): bool
    {
        $schemas = $this->getObjectModelDefinition()->getSchemas();

        return $this->createTables($schemas);
    }

    /**
     * @see \CW\ObjectModel\ExtensionInterface
     */
    public function uninstall(): bool
    {
        $names = $this->getObjectModelDefinition()->getNames();

        return $this->dropTables(array_reverse($names));
    }

    /**
     * Get \CW\ObjectModel\Definition (as an array collection object).
     */
    protected function getObjectModelDefinition(): Definition
    {
        return new Definition($this->om->getDefinition($this->om));
    }

    /**
     * Create tables.
     */
    protected function createTables(array $schemas): bool
    {
        return array_product(array_map([$this, 'createTable'], $schemas));
    }

    /**
     * Create table.
     */
    protected function createTable(Schema $schema): bool
    {
        return (new Table($this->db))->hydrate($schema)->create();
    }

    /**
     * Drop tables.
     */
    protected function dropTables(array $names): bool
    {
        return array_product(array_map([$this, 'dropTable'], $names));
    }

    /**
     * Drop table.
     */
    protected function dropTable(string $name): bool
    {
        return (new Table($this->db))->setName($name)->drop();
    }
}
