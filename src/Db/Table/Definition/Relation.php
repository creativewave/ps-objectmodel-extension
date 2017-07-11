<?php

namespace CW\Db\Table\Definition;

class Relation implements RelationInterface
{
    /**
     * Internal table ID.
     *
     * @var string
     */
    protected $id;

    /**
     * @var \CW\ObjectModel\Definition
     */
    protected $def;

    /**
     * Register \CW\ObjectModel\Definition and the internal ID.
     */
    public function __construct(\CW\ObjectModel\Definition $def, string $id)
    {
        $this->id  = $id;
        $this->def = $def;
    }

    /**
     * Get key value from \ObjectModel::$definition['associations'][$this->id].
     */
    public function get(string $key)
    {
        switch ($key) {
            case 'fields':
                return $this->def->get('associations')[$this->id][$key] ?? [];
            default:
                return $this->def->get('associations')[$this->id][$key] ?? null;
        }
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getName()
     */
    public function getName(): string
    {
        switch ($this->id) {
            case static::ID_LANG:
                return $this->def->getModel()->getName().'_lang';
            case static::ID_SHOP:
                return $this->def->getModel()->getName().'_shop';
            default:
                return static::DB_PREFIX.$this->get('association');
        }
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getPrimary()
     */
    public function getPrimary(): string
    {
        switch ($this->id) {
            case static::ID_LANG:
                return 'id_lang';
            case static::ID_SHOP:
                return 'id_shop';
            default:
                return $this->get('field');
        }
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getEngine()
     */
    public function getEngine(): string
    {
        return $this->get('engine') ?? static::ENGINE;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getCharset()
     */
    public function getCharset(): string
    {
        return $this->get('charset') ?? static::CHARSET;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getCollation()
     */
    public function getCollation(): string
    {
        return $this->get('collation') ?? static::COLLATION;
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
        $primary = [
            $this->def->getModel()->getPrimary(),
            $this->getPrimary(),
        ];
        $this->hasMany('shop') and $primary[] = 'id_shop';
        $this->hasMany('lang') and $primary[] = 'id_lang';

        return $primary;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysForeign()
     */
    public function getKeysForeign(): array
    {
        $model_table_1   = $this->def->getModel()->getName();
        $model_primary_1 = $this->def->getModel()->getPrimary();
        $model_table_2   = $this->getForeignTable();
        $model_primary_2 = $this->getPrimary();

        $foreign = [
            ["$model_table_1.$model_primary_1"],
            ["$model_table_2.$model_primary_2"],
        ];
        $this->hasMany('shop') and $foreign[] = [_DB_PREFIX_.'shop.id_shop'];
        $this->hasMany('lang') and $foreign[] = [_DB_PREFIX_.'lang.id_lang'];

        return $foreign;
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysSimple()
     */
    public function getKeysSimple(): array
    {
        switch ($this->id) {
            case static::ID_LANG:
            case static::ID_SHOP:
                return [];
            default:
                return array_filter($this->get('fields'), [$this->def, 'isFieldSimpleKey']);
        }
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysUnique()
     */
    public function getKeysUnique(): array
    {
        switch ($this->id) {
            case static::ID_LANG:
            case static::ID_SHOP:
                return [];
            default:
                return array_filter($this->getFieldsCommon(), [$this->def, 'isFieldUniqueKey']);
        }
    }

    /**
     * @see \CW\Db\Table\Definition\DefinitionInterface::getKeysFulltext()
     */
    public function getKeysFulltext(): array
    {
        switch ($this->id) {
            case static::ID_LANG:
            case static::ID_SHOP:
                return [];
            default:
                return array_filter($this->getFieldsCommon(), [$this->def, 'isFieldFulltextKey']);
        }
    }

    /**
     * @see \CW\Db\Table\Definition\RelationInterface::getType()
     */
    public function getType(): int
    {
        switch ($this->id) {
            case static::ID_LANG:
            case static::ID_SHOP:
                return \ObjectModel::HAS_MANY;
            default:
                return $this->get('type');
        }
    }

    /**
     * Get relation fields.
     */
    protected function getFields(): array
    {
        return array_merge($this->getFieldsPrimary(), $this->getFieldsCommon());
    }

    /**
     * Get relation primary fields.
     */
    protected function getFieldsPrimary(): array
    {
        $fields = [
            $this->def->getModel()->getPrimary() => static::KEY_FIELD,
            $this->getPrimary() => static::KEY_FIELD,
        ];
        $this->hasMany('shop') and $fields['id_shop'] = static::KEY_FIELD;
        $this->hasMany('lang') and $fields['id_lang'] = static::KEY_FIELD;

        return $fields;
    }

    /**
     * Get relation common fields.
     */
    protected function getFieldsCommon(): array
    {
        switch ($this->id) {
            case static::ID_LANG:
            case static::ID_SHOP:
                return array_filter($this->def->get('fields'), [$this, 'hasField'], ARRAY_FILTER_USE_BOTH);
            default:
                return $this->get('fields');
        }
    }

    /**
     * Get relation foreign table.
     */
    protected function getForeignTable(): string
    {
        switch ($this->id) {
            case static::ID_LANG:
                return _DB_PREFIX_.'lang';
            case static::ID_SHOP:
                return _DB_PREFIX_.'shop';
            default:
                return _DB_PREFIX_.$this->getForeignModelTableName();
        }
    }

    /**
     * Get table name of foreign relation model.
     */
    protected function getForeignModelTableName(): string
    {
        $class = '\\'.$this->get('object');

        return $class::$definition['table'];
    }

    /**
     * Wether or not this table has a 'OneToMany' relation.
     */
    protected function hasMany(string $relation): bool
    {
        switch ($this->id) {
            case static::ID_LANG:
                return !empty($this->def->get("multilang_$relation"));
            default:
                return !empty($this->get("multi$relation"));
        }
    }

    /**
     * Wether or not this table has a given field.
     */
    protected function hasField(array $field, string $name): bool
    {
        switch ($this->id) {
            case static::ID_LANG:
                return !empty($field['lang']);
            case static::ID_SHOP:
                return !empty($field['shop']);
            default:
                return isset($this->get('fields')[$name]);
        }
    }
}
