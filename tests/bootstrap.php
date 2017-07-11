<?php

define('_DB_PREFIX_', 'ps_');
define('_MYSQL_ENGINE_', 'InnoDB');

class Db
{
    public function execute(string $query): bool
    {
        return true;
    }
}
class ObjectModel
{
    const HAS_ONE  = 1;
    const HAS_MANY = 2;
    const TYPE_INT     = 1;
    const TYPE_BOOL    = 2;
    const TYPE_STRING  = 3;
    const TYPE_FLOAT   = 4;
    const TYPE_DATE    = 5;
    const TYPE_HTML    = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL     = 8;

    public static $definition;

    public function getDefinition(): array
    {
        if (!empty(static::$definition['multilang'])) {
            static::$definition['associations']['l'] = [
                'type' => static::HAS_MANY,
                'field' => static::$definition['primary'],
                'foreign_field' => static::$definition['primary'],
            ];
        }

        return static::$definition;
    }
}
class OtherModel extends ObjectModel
{
    public static $definition = ['table' => 'other_model'];
}
