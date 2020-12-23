<?php


namespace Phespro\Phespro\Migration\MigrationStateStorage;


use Phespro\Phespro\Migration\MigrationStateStorageInterface;

class SQLiteMigrationStateStorage implements MigrationStateStorageInterface
{
    public function __construct(protected \PDO $conn)
    {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id TEXT PRIMARY KEY                
            );
        ");
    }

    function add(string $id): void
    {
        $stmt = $this->conn->prepare("
            INSERT INTO migrations VALUES (:id)
        ");
        $stmt->execute(['id' => $id]);
    }

    function contains(string $id): bool
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM migrations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return !!$stmt->fetchColumn();
    }
}