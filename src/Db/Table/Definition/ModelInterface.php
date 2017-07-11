<?php

namespace CW\Db\Table\Definition;

interface ModelInterface extends DefinitionInterface
{
    /**
     * Internal ID.
     */
    const ID = 'm';

    /**
     * Wether or not a model has a relation.
     */
    public function has(string $relation): bool;

    /**
     * Wether or not a model relation type is "ManyToMany".
     */
    public function hasMany(string $relation): bool;

    /**
     * Wether or not a model relation type is "OneToMany" or "OneToOne".
     */
    public function hasSingle(string $relation): bool;
}
