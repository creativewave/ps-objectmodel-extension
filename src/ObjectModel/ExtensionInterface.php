<?php

namespace CW\ObjectModel;

interface ExtensionInterface
{
    /**
     * Install \ObjectModel.
     */
    public function install(): bool;

    /**
     * Uninstall \ObjectModel.
     */
    public function uninstall(): bool;
}
