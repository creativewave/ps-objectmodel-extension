<?php

namespace CW\Db\Table;

class Table
{
    /**
     * Key identifiers.
     *
     * @var int
     */
    const PRIMARY  = 1;
    const FOREIGN  = 2;
    const UNIQUE   = 3;
    const FULLTEXT = 4;

    /** @var string */
    protected $name;
    /** @var string */
    protected $engine;
    /** @var string */
    protected $charset;
    /** @var string */
    protected $collation;
    /** @var array */
    protected $columns;
    /** @var array */
    protected $keys;

    /**
     * Register \Db.
     */
    public function __construct(\Db $db)
    {
        $this->db = $db;
    }

    /**
     * Hydrate properties.
     */
    public function hydrate(Schema $schema): self
    {
        return $schema->map($this);
    }

    /**
     * Create table.
     */
    public function create(): bool
    {
        return $this->db->execute("CREATE TABLE IF NOT EXISTS `$this->name` (".
            implode(', ', array_merge($this->columns, $this->keys)).
        ") ENGINE=$this->engine CHARSET=$this->charset COLLATE=$this->collation;");
    }

    /**
     * Drop table.
     */
    public function drop(): bool
    {
        return $this->db->execute("DROP TABLE IF EXISTS `$this->name`;");
    }

    /**
     * Set name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set engine.
     */
    public function setEngine(string $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Set character set.
     */
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Set collation.
     */
    public function setCollation(string $collation): self
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * Set columns.
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set primary key.
     */
    public function setKeyPrimary(array $columns): self
    {
        return $this->setKey($columns, static::PRIMARY);
    }

    /**
     * Set foreign keys.
     */
    public function setKeysForeign(array $keys): self
    {
        foreach ($keys as $columns) {
            $this->setKeyForeign($columns);
        }

        return $this;
    }

    /**
     * Set foreign key.
     */
    public function setKeyForeign(array $columns): self
    {
        return $this->setKey($columns, static::FOREIGN);
    }

    /**
     * Set unique keys.
     */
    public function setKeysUnique(array $keys): self
    {
        return $this->setKeyUnique(array_keys($keys));
    }

    /**
     * Set unique key.
     */
    public function setKeyUnique(array $columns): self
    {
        return $this->setKey($columns, static::UNIQUE);
    }

    /**
     * Set fulltext keys.
     */
    public function setKeysFulltext(array $keys): self
    {
        return $this->setKeyFulltext(array_keys($keys));
    }

    /**
     * Set fulltext key.
     */
    public function setKeyFulltext(array $columns): self
    {
        return $this->setKey($columns, static::FULLTEXT);
    }

    /**
     * Set (simple) keys.
     */
    public function setKeysSimple(array $keys): self
    {
        return $this->setKeySimple(array_keys($keys));
    }

    /**
     * Set (simple) key.
     */
    public function setKeySimple(array $columns): self
    {
        return $this->setKey($columns);
    }

    /**
     * Set key.
     */
    protected function setKey(array $columns, int $type = null): self
    {
        // Empty columns may be returned by `array_filter`s.
        if (empty($columns)) {
            return $this;
        }

        $name = implode('_', $columns);
        $columns = implode('`, `', $columns);
        switch ($type) {
            case static::PRIMARY:
                $this->keys[] = "PRIMARY KEY (`$columns`)";
                break;
            case static::FOREIGN:
                list($table, $columns) = explode('.', $name);
                $this->keys[] = "FOREIGN KEY (`$columns`) REFERENCES $table (`$columns`) ON UPDATE CASCADE ON DELETE CASCADE";
                break;
            case static::UNIQUE:
                $this->keys[] = "UNIQUE KEY (`$columns`)";
                break;
            case static::FULLTEXT:
                $this->keys[] = "FULLTEXT KEY (`$columns`)";
                break;
            default:
                $this->keys[] = "KEY (`$columns`)";
                break;
        }

        return $this;
    }
}
