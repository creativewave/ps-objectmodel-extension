<?php

use CW\ObjectModel\Extension;
use PHPUnit\Framework\TestCase;

require dirname(__DIR__).'/vendor/autoload.php';

class ObjectModelExtensionTest extends TestCase
{
    public $table     = 'my_model';
    public $primary   = 'id_my_model';
    public $charset   = 'utf8mb4';
    public $collation = 'DEFAULT';

    /**
     * Provide data to ObjectModelExtensionTest::testInstall().
     */
    public function provideInstall()
    {
        return [
            'single' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'multishop' => [
                'definition' => [
                    'table'     => $this->table,
                    'primary'   => $this->primary,
                    'multishop' => true,
                    'fields'    => [
                        'shop' => ['type' => \ObjectModel::TYPE_INT, 'shop' => true],
                        'both' => ['type' => \ObjectModel::TYPE_INT, 'shop' => 'both'],
                    ],
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`both` INT(10) SIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    "CREATE TABLE IF NOT EXISTS `ps_{$this->table}_shop` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_shop` INT(10) UNSIGNED NOT NULL, '.
                        '`shop` INT(10) SIGNED NOT NULL, '.
                        '`both` INT(10) SIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_shop`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_shop`) REFERENCES ps_shop (`id_shop`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'multilang' => [
                'definition' => [
                    'table'     => $this->table,
                    'primary'   => $this->primary,
                    'multilang' => true,
                    'fields'    => [
                        'lang' => ['type' => \ObjectModel::TYPE_INT, 'lang' => true],
                    ],
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    "CREATE TABLE IF NOT EXISTS `ps_{$this->table}_lang` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_lang` INT(10) UNSIGNED NOT NULL, '.
                        '`lang` INT(10) SIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_lang`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_lang`) REFERENCES ps_lang (`id_lang`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'multilang_shop' => [
                'definition' => [
                    'table'          => $this->table,
                    'primary'        => $this->primary,
                    'multilang'      => true,
                    'multilang_shop' => true,
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    "CREATE TABLE IF NOT EXISTS `ps_{$this->table}_lang` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_lang` INT(10) UNSIGNED NOT NULL, '.
                        '`id_shop` INT(10) UNSIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_lang`, `id_shop`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_lang`) REFERENCES ps_lang (`id_lang`) ON UPDATE CASCADE ON DELETE CASCADE, '.
                        'FOREIGN KEY (`id_shop`) REFERENCES ps_shop (`id_shop`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'multishop_multilang_shop' => [
                'definition' => [
                    'table'          => $this->table,
                    'primary'        => $this->primary,
                    'multilang'      => true,
                    'multilang_shop' => true,
                    'multishop'      => true,
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    "CREATE TABLE IF NOT EXISTS `ps_{$this->table}_lang` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_lang` INT(10) UNSIGNED NOT NULL, '.
                        '`id_shop` INT(10) UNSIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_lang`, `id_shop`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_lang`) REFERENCES ps_lang (`id_lang`) ON UPDATE CASCADE ON DELETE CASCADE, '.
                        'FOREIGN KEY (`id_shop`) REFERENCES ps_shop (`id_shop`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    "CREATE TABLE IF NOT EXISTS `ps_{$this->table}_shop` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_shop` INT(10) UNSIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_shop`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_shop`) REFERENCES ps_shop (`id_shop`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'association' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'associations' => [
                        'association' => [
                            'type'        => \ObjectModel::HAS_MANY,
                            'object'      => 'OtherModel',
                            'association' => 'model_other_model',
                            'field'       => 'id_other_model',
                            'multilang'   => true,
                            'multishop'   => true,
                            'fields'      => [
                                'field' => ['type' => \ObjectModel::TYPE_INT],
                            ],
                        ],
                    ],
                ],
                'queries'    => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                    'CREATE TABLE IF NOT EXISTS `ps_model_other_model` ('.
                        "`$this->primary` INT(10) UNSIGNED NOT NULL, ".
                        '`id_other_model` INT(10) UNSIGNED NOT NULL, '.
                        '`id_shop` INT(10) UNSIGNED NOT NULL, '.
                        '`id_lang` INT(10) UNSIGNED NOT NULL, '.
                        '`field` INT(10) SIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`, `id_other_model`, `id_shop`, `id_lang`), ".
                        "FOREIGN KEY (`$this->primary`) REFERENCES ps_$this->table (`$this->primary`) ON UPDATE CASCADE ON DELETE CASCADE, ".
                        'FOREIGN KEY (`id_other_model`) REFERENCES ps_other_model (`id_other_model`) ON UPDATE CASCADE ON DELETE CASCADE, '.
                        'FOREIGN KEY (`id_shop`) REFERENCES ps_shop (`id_shop`) ON UPDATE CASCADE ON DELETE CASCADE, '.
                        'FOREIGN KEY (`id_lang`) REFERENCES ps_lang (`id_lang`) ON UPDATE CASCADE ON DELETE CASCADE'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_bool' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'bool'          => ['type' => \ObjectModel::TYPE_BOOL],
                        'bool_default'  => ['type' => \ObjectModel::TYPE_BOOL, 'default' => 1],
                        'bool_null'     => ['type' => \ObjectModel::TYPE_BOOL, 'allow_null' => true],
                        'bool_unsigned' => ['type' => \ObjectModel::TYPE_BOOL, 'validate' => 'isUnsigned'],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`bool` TINYINT(1) UNSIGNED NOT NULL, '.
                        "`bool_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1', ".
                        '`bool_null` TINYINT(1) UNSIGNED, '.
                        '`bool_unsigned` TINYINT(1) UNSIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_date' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'date'         => ['type' => \ObjectModel::TYPE_DATE],
                        'date_default' => ['type' => \ObjectModel::TYPE_DATE, 'default' => '0000-00-00'],
                        'date_null'    => ['type' => \ObjectModel::TYPE_DATE, 'allow_null' => true],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`date` DATETIME NOT NULL, '.
                        "`date_default` DATETIME NOT NULL DEFAULT '0000-00-00', ".
                        '`date_null` DATETIME, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_float' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'float'         => ['type' => \ObjectModel::TYPE_FLOAT],
                        'float_null'    => ['type' => \ObjectModel::TYPE_FLOAT, 'allow_null' => true],
                        'float_size'    => ['type' => \ObjectModel::TYPE_FLOAT, 'size' => 6, 'scale' => 6],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`float` DECIMAL NOT NULL, '.
                        '`float_null` DECIMAL, '.
                        '`float_size` DECIMAL(6,6) NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_int' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'int'          => ['type' => \ObjectModel::TYPE_INT],
                        'int_default'  => ['type' => \ObjectModel::TYPE_INT, 'default' => 1],
                        'int_enum'     => ['type' => \ObjectModel::TYPE_INT, 'values' => [1, 2, 3]],
                        'int_null'     => ['type' => \ObjectModel::TYPE_INT, 'allow_null' => true],
                        'int_unsigned' => ['type' => \ObjectModel::TYPE_INT, 'validate' => 'isUnsigned'],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`int` INT(10) SIGNED NOT NULL, '.
                        "`int_default` INT(10) SIGNED NOT NULL DEFAULT '1', ".
                        "`int_enum` INT(10) SIGNED NOT NULL ENUM('1','2','3'), ".
                        '`int_null` INT(10) SIGNED, '.
                        '`int_unsigned` INT(10) UNSIGNED NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_html' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'html'            => ['type' => \ObjectModel::TYPE_HTML],
                        'html_default'    => ['type' => \ObjectModel::TYPE_HTML, 'default' => '<p>"Hello World!"</p>'],
                        'html_null'       => ['type' => \ObjectModel::TYPE_HTML, 'allow_null' => true],
                        'html_size'       => ['type' => \ObjectModel::TYPE_HTML, 'size' => 65535],
                        'html_size_range' => ['type' => \ObjectModel::TYPE_HTML, 'size' => ['min' => 200, 'max' => 1000]],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`html` TEXT NOT NULL, '.
                        "`html_default` TEXT NOT NULL DEFAULT '<p>\\\"Hello World!\\\"</p>', ".
                        '`html_null` TEXT, '.
                        '`html_size` TEXT(65535) NOT NULL, '.
                        '`html_size_range` TEXT(1000) NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'field_type_string' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'string'            => ['type' => \ObjectModel::TYPE_STRING],
                        'string_default'    => ['type' => \ObjectModel::TYPE_STRING, 'default' => '<p>"Hello World!"</p>'],
                        'string_null'       => ['type' => \ObjectModel::TYPE_STRING, 'allow_null' => true],
                        'string_size'       => ['type' => \ObjectModel::TYPE_STRING, 'size' => 255],
                        'string_size_range' => ['type' => \ObjectModel::TYPE_STRING, 'size' => ['min' => 10, 'max' => 20]],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`string` VARCHAR(255) NOT NULL, '.
                        "`string_default` VARCHAR(255) NOT NULL DEFAULT '<p>\\\"Hello World!\\\"</p>', ".
                        '`string_null` VARCHAR(255), '.
                        '`string_size` VARCHAR(255) NOT NULL, '.
                        '`string_size_range` VARCHAR(20) NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`)".
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
            'custom_keys' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'fields'  => [
                        'simple'   => ['type' => \ObjectModel::TYPE_INT, 'key' => true],
                        'unique'   => ['type' => \ObjectModel::TYPE_INT, 'unique' => true],
                        'fulltext' => ['type' => \ObjectModel::TYPE_STRING, 'fulltext' => true],
                    ],
                ],
                'queries' => [
                    "CREATE TABLE IF NOT EXISTS `ps_$this->table` (".
                        "`$this->primary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                        '`simple` INT(10) SIGNED NOT NULL, '.
                        '`unique` INT(10) SIGNED NOT NULL, '.
                        '`fulltext` VARCHAR(255) NOT NULL, '.
                        "PRIMARY KEY (`$this->primary`), ".
                        'KEY (`simple`), '.
                        'UNIQUE KEY (`unique`), '.
                        'FULLTEXT KEY (`fulltext`)'.
                    ") ENGINE=InnoDB CHARSET=$this->charset COLLATE=$this->collation;",
                ],
            ],
        ];
    }

    /**
     * CW\ObjectModel\Extension::install() should trigger expected DB queries.
     *
     * @dataProvider provideInstall
     */
    public function testInstall(array $definition, array $queries)
    {
        $model = new ObjectModel();
        $model::$definition = $definition;
        $db = $this->createMock('Db');
        $db
            ->expects($this->exactly(count($queries)))
            ->method('execute')
            ->withConsecutive(...array_map(function ($query) {
                return [$this->equalTo($query)];
            }, $queries));

        (new Extension($model, $db))->install();
    }

    /**
     * Provide data to ObjectModelExtensionTest::testUninstall().
     */
    public function provideUninstall()
    {
        return [
            'single' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                ],
                'queries'    => [
                    "DROP TABLE IF EXISTS `ps_$this->table`;",
                ],
            ],
            'multishop' => [
                'definition' => [
                    'table'     => $this->table,
                    'primary'   => $this->primary,
                    'multishop' => true,
                ],
                'queries'    => [
                    "DROP TABLE IF EXISTS `ps_{$this->table}_shop`;",
                    "DROP TABLE IF EXISTS `ps_$this->table`;",
                ],
            ],
            'multilang' => [
                'definition' => [
                    'table'     => $this->table,
                    'primary'   => $this->primary,
                    'multilang' => true,
                ],
                'queries'    => [
                    "DROP TABLE IF EXISTS `ps_{$this->table}_lang`;",
                    "DROP TABLE IF EXISTS `ps_$this->table`;",
                ],
            ],
            'association' => [
                'definition' => [
                    'table'   => $this->table,
                    'primary' => $this->primary,
                    'associations' => [
                        'association' => [
                            'type'        => \ObjectModel::HAS_MANY,
                            'object'      => 'OtherModel',
                            'association' => 'model_other_model',
                            'field'       => 'id_other_model',
                        ],
                    ],
                ],
                'queries'    => [
                    'DROP TABLE IF EXISTS `ps_model_other_model`;',
                    "DROP TABLE IF EXISTS `ps_$this->table`;",
                ],
            ],
        ];
    }

    /**
     * CW\ObjectModel\Extension::uninstall() should trigger expected DB queries.
     *
     * @dataProvider provideUninstall
     */
    public function testUninstall(array $definition, array $queries)
    {
        $model = new ObjectModel();
        $model::$definition = $definition;
        $db = $this->createMock('Db');
        $db
            ->expects($this->exactly(count($queries)))
            ->method('execute')
            ->withConsecutive(...array_map(function ($query) {
                return [$this->equalTo($query)];
            }, $queries));

        (new Extension($model, $db))->uninstall();
    }
}
