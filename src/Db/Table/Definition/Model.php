<?php

namespace CW\Db\Table\Definition;

class Model implements ModelInterface
{
    /**
     * @var \CW\ObjectModel\Definition
     */
    protected $def;

    /**
     * Register \CW\ObjectModel\Definition.
     */
    public function __construct(\CW\ObjectModel\Definition $def)
    {
        $this->def = $def;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getName()
     */
    public function getName(): string
    {
        return static::DB_PREFIX.$this->def->get('table');
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getPrimary()
     */
    public function getPrimary(): string
    {
        return $this->def->get('primary');
    }

    /**
     * Get engine.
     */
    public function getEngine(): string
    {
        return $this->def->get('engine') ?? static::ENGINE;
    }

    /**
     * Get charset.
     */
    public function getCharset(): string
    {
        return $this->def->get('charset') ?? static::CHARSET;
    }

    /**
     * Get collation.
     */
    public function getCollation(): string
    {
        return $this->def->get('collation') ?? static::COLLATION;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getColumns()
     */
    public function getColumns(): array
    {
        return $this->def->getColumnsFromFields($this->getFields());
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeyPrimary()
     */
    public function getKeyPrimary(): array
    {
        return [$this->getPrimary()];
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysForeign()
     */
    public function getKeysForeign(): array
    {
        $ids = $this->def->getIdsSingleRelations();
        $relations = $this->def->getRelations($ids);

        return array_map(function (Relation $relation) {
            return ["{$relation->getName()}.{$relation->getPrimary()}"];
        }, $relations);
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysSimple()
     */
    public function getKeysSimple(): array
    {
        return array_filter($this->getFieldsCommon(), [$this->def, 'isFieldSimpleKey']);
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysUnique()
     */
    public function getKeysUnique(): array
    {
        return array_filter($this->getFieldsCommon(), [$this->def, 'isFieldUniqueKey']);
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysFulltext()
     */
    public function getKeysFulltext(): array
    {
        return array_filter($this->getFieldsCommon(), [$this->def, 'isFieldFulltextKey']);
    }

    /**
     * @see \CW\Db\Table\Definition\ModelInterface::has()
     */
    public function has(string $relation): bool
    {
        switch ($relation) {
            case Relation::ID_LANG:
                return $this->def->get('multilang');
            case Relation::ID_SHOP:
                return $this->def->get('multishop');
            default:
                return isset($this->def->get('associations')[$relation]);
        }
    }

    /**
     * @see \CW\Db\Table\Definition\ModelInterface::hasMany()
     */
    public function hasMany(string $relation): bool
    {
        return $this->has($relation)
               and \ObjectModel::HAS_MANY === $this->def->getRelation($relation)->getType();
    }

    /**
     * @see \CW\Db\Table\Definition\ModelInterface::hasSingle()
     */
    public function hasSingle(string $relation): bool
    {
        return $this->has($relation)
               and \ObjectModel::HAS_ONE === $this->def->getRelation($relation)->getType();
    }

    /**
     * Get fields.
     */
    protected function getFields(): array
    {
        return array_merge($this->getFieldPrimary(), $this->getFieldsCommon());
    }

    /**
     * Get primary field.
     */
    protected function getFieldPrimary(): array
    {
        return [$this->getPrimary() => static::PRIMARY_KEY_FIELD];
    }

    /**
     * Get common fields.
     */
    protected function getFieldsCommon(): array
    {
        return array_filter($this->def->get('fields'), [$this, 'hasField']);
    }

    /**
     * Wether or not this table has a given field.
     */
    protected function hasField(array $field): bool
    {
        return !$this->isFieldMultilang($field)
               and !$this->isFieldMultishop($field)
               or $this->isFieldMultishopShared($field);
    }

    /**
     * Wether or not given field is multilang.
     */
    protected function isFieldMultilang(array $field): bool
    {
        return !empty($field['lang']);
    }

    /**
     * Wether or not given field is multishop.
     */
    protected function isFieldMultishop(array $field): bool
    {
        return !empty($field['shop']);
    }

    /**
     * Wether or not given multishop field is shared with this table.
     */
    protected function isFieldMultishopShared(array $field): bool
    {
        return !empty($field['shop']) and 'both' === $field['shop'];
    }
}
