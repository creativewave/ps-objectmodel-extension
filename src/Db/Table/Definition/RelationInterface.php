<?php

namespace CW\Db\Table\Definition;

interface RelationInterface extends DefinitionInterface
{
    /**
     * Internal IDs.
     */
    const ID_LANG = 'l';
    const ID_SHOP = 's';

    /**
     * Get relation type.
     */
    public function getType(): int;
}
