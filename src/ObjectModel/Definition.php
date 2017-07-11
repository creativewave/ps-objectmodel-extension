<?php

namespace CW\ObjectModel;

use CW\Db\Table\Definition\Model;
use CW\Db\Table\Definition\Relation;
use CW\Db\Table\Schema;

class Definition implements DefinitionInterface
{
    /**
     * \ObjectModel::$definition.
     *
     * @var array
     */
    protected $def;

    /**
     * @var \CW\Db\Table\Definition\Model
     */
    protected $model;

    /**
     * @var array of \CW\Db\Table\Definition\Relation
     */
    protected $relations;

    /**
     * Register \ObjectModel::$definition as a collection data source.
     */
    public function __construct(array $def)
    {
        $this->def = $def;
        // Prestashop doesn't define shop association when fetching definition.
        if ($this->get('multishop')) {
            $this->def['associations'][Relation::ID_SHOP] = [
                'type'  => \ObjectModel::HAS_MANY,
                'field' => $this->get('primary'),
            ];
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::get()
     */
    public function get(string $key)
    {
        switch ($key) {
            case 'table':
            case 'primary':
                return $this->def[$key];
            case 'fields':
            case 'associations':
                return $this->def[$key] ?? [];
            case 'multilang':
            case 'multilang_shop':
            case 'multishop':
                return $this->def[$key] ?? false;
            default:
                return $this->def[$key] ?? null;
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getSchemas()
     */
    public function getSchemas(): array
    {
        return array_map([$this, 'getSchema'], $this->getIds());
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getSchema()
     */
    public function getSchema(string $id): Schema
    {
        return new Schema($this, $id);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getModel()
     */
    public function getModel(): Model
    {
        return $this->model ?? $this->model = new Model($this);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getRelations()
     */
    public function getRelations(array $ids): array
    {
        return array_map([$this, 'getRelation'], $ids);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getRelation()
     */
    public function getRelation(string $id): Relation
    {
        return $this->relations[$id] ??
               $this->relations[$id] = new Relation($this, $id);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getIds()
     */
    public function getIds(): array
    {
        return array_unique(array_merge([$this->getIdModel()], $this->getIdsMultiRelations()));
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getIdModel()
     */
    public function getIdModel(): string
    {
        return Model::ID;
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getIdsRelations()
     */
    public function getIdsRelations(int $type = null): array
    {
        switch ($type) {
            // OneToOne and ManyToOne.
            case \ObjectModel::HAS_ONE:
                return array_filter($this->getIdsRelations(), [$this->getModel(), 'hasSingle']);
            // ManyToMany.
            case \ObjectModel::HAS_MANY:
                return array_filter($this->getIdsRelations(), [$this->getModel(), 'hasMany']);
            // All potential relations.
            default:
                return array_merge([Relation::ID_LANG, Relation::ID_SHOP], array_keys($this->get('associations')));
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getIdsMultiRelations()
     */
    public function getIdsMultiRelations(): array
    {
        return $this->getIdsRelations(\ObjectModel::HAS_MANY);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getIdsSingleRelations()
     */
    public function getIdsSingleRelations(): array
    {
        return $this->getIdsRelations(\ObjectModel::HAS_ONE);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getNames()
     */
    public function getNames(): array
    {
        return array_map([$this, 'getName'], $this->getIds());
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getName()
     */
    public function getName(string $id): string
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getName();
            default:
                return $this->getRelation($id)->getName();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getEngine()
     */
    public function getEngine(string $id): string
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getEngine();
            default:
                return $this->getRelation($id)->getEngine();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getCharset()
     */
    public function getCharset(string $id): string
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getCharset();
            default:
                return $this->getRelation($id)->getCharset();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getCollation()
     */
    public function getCollation(string $id): string
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getCollation();
            default:
                return $this->getRelation($id)->getCollation();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getColumns()
     */
    public function getColumns(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getColumns();
            default:
                return $this->getRelation($id)->getColumns();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getColumnsFromFields()
     */
    public function getColumnsFromFields(array $fields): array
    {
        return array_map([$this, 'getColumnFromField'], array_keys($fields), $fields);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getColumnFromField()
     *
     * @todo handle `size` constraint for setting a numeric type and its length.
     */
    public function getColumnFromField(string $name, array $constraints): string
    {
        $description = "`$name` ";
        switch ($constraints['type']) {
            case \ObjectModel::TYPE_BOOL:
                $description .= 'TINYINT(1) UNSIGNED';
                break;
            case \ObjectModel::TYPE_DATE:
                $description .= 'DATETIME';
                break;
            case \ObjectModel::TYPE_FLOAT:
                $description .= 'DECIMAL'.(
                    isset($constraints['size'], $constraints['scale'])
                        ? "({$constraints['size']},{$constraints['scale']})"
                        : ''
                );
                break;
            case \ObjectModel::TYPE_HTML:
            case \ObjectModel::TYPE_SQL:
                $length = $constraints['size']['max'] ?? $constraints['size'] ?? null;
                $description .= $length ? "TEXT($length)" : 'TEXT';
                break;
            case \ObjectModel::TYPE_INT:
                $description .= 'INT(10)'.(
                    !empty($constraints['validate']) && strpos($constraints['validate'], 'Unsigned')
                        ? ' UNSIGNED'
                        : ' SIGNED'
                );
                break;
            case \ObjectModel::TYPE_STRING:
                $length = $constraints['size']['max'] ?? $constraints['size'] ?? '255';
                $description .= "VARCHAR($length)";
                break;
            default:
                throw new PrestaShopException("Missing type constraint definition for field $name");
        }
        if (empty($constraints['allow_null']) or isset($constraints['default']) or !empty($constraints['required'])) {
            $description .= ' NOT NULL';
        }
        if (!empty($constraints['values'])) {
            $description .= " ENUM('".implode("','", $constraints['values'])."')";
        }
        if (isset($constraints['default'])) {
            $description .= " DEFAULT '".addslashes($constraints['default'])."'";
        }
        if (!empty($constraints['primary'])) {
            $description .= ' AUTO_INCREMENT';
        }

        return $description;
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getKeyPrimary()
     */
    public function getKeyPrimary(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getKeyPrimary();
            default:
                return $this->getRelation($id)->getKeyPrimary();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getKeysForeign()
     */
    public function getKeysForeign(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getKeysForeign();
            default:
                return $this->getRelation($id)->getKeysForeign();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getKeysSimple()
     */
    public function getKeysSimple(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getKeysSimple();
            default:
                return $this->getRelation($id)->getKeysSimple();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getKeysUnique()
     */
    public function getKeysUnique(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getKeysUnique();
            default:
                return $this->getRelation($id)->getKeysUnique();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::getKeysFulltext()
     */
    public function getKeysFulltext(string $id): array
    {
        switch ($id) {
            case Model::ID:
                return $this->getModel()->getKeysFulltext();
            default:
                return $this->getRelation($id)->getKeysFulltext();
        }
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::isFieldSimpleKey()
     */
    public function isFieldSimpleKey(array $field): bool
    {
        return !empty($field['key']);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::isFieldUniqueKey()
     */
    public function isFieldUniqueKey(array $field): bool
    {
        return !empty($field['unique']);
    }

    /**
     * @see \CW\ObjectModel\DefinitionInterface::isFieldFulltextKey()
     */
    public function isFieldFulltextKey(array $field): bool
    {
        return !empty($field['fulltext']);
    }
}
