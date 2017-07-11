<?php

namespace CW\Db\Table;

use CW\ObjectModel\Definition as Definition;

class Schema
{
    /**
     * @var CW\ObjectModel\Definition
     */
    protected $def;

    /**
     * Table (internal) ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Register \CW\ObjectModel\Definition and table (internal) ID.
     */
    public function __construct(Definition $def, string $id)
    {
        $this->def = $def;
        $this->id  = $id;
    }

    /**
     * Map table properties.
     */
    public function map(Table $table): Table
    {
        return $table
            ->setName($this->def->getName($this->id))
            ->setEngine($this->def->getEngine($this->id))
            ->setCharset($this->def->getCharset($this->id))
            ->setCollation($this->def->getCollation($this->id))
            ->setColumns($this->def->getColumns($this->id))
            ->setKeyPrimary($this->def->getKeyPrimary($this->id))
            ->setKeysForeign($this->def->getKeysForeign($this->id))
            ->setKeysSimple($this->def->getKeysSimple($this->id))
            ->setKeysUnique($this->def->getKeysUnique($this->id))
            ->setKeysFulltext($this->def->getKeysFulltext($this->id));
    }
}
