<?php

namespace Phespro\Phespro\Migration\MigrationStateStorage;

use Phespro\Phespro\Migration\MigrationStateStorageInterface;

/**
 * Primarily for testing purposes
 * 
 * 
 * @package Phespro\Phespro\Migration\MigrationStateStorage
 */
class MemoryMigrationStateStorage implements MigrationStateStorageInterface
{
    private array $migrations = [];

    function add(string $id): void
    {
        $this->migrations[] = $id;
    }

    function contains(string $id): bool
    {
        return in_array($id, $this->migrations);
    }
}