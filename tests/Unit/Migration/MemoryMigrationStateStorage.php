<?php


namespace Phespro\Phespro\Tests\Unit\Migration;


use Phespro\Phespro\Migration\MigrationStateStorageInterface;

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