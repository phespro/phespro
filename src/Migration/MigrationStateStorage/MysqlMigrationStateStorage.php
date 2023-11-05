<?php

namespace Phespro\Phespro\Migration\MigrationStateStorage;

use Phespro\Phespro\Migration\MigrationStateStorageInterface;

readonly class MysqlMigrationStateStorage implements MigrationStateStorageInterface
{
    public function __construct(
        protected \PDO $pdo,
    )
    {
    }

    public function add(string $id): void
    {
        $this->pdo->prepare("
            INSERT INTO migration(id)
            VALUES (:id)
        ")->execute(['id' => $id]);
    }

    public function contains(string $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migration WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return !!$stmt->fetchColumn();
    }

    public function getPassed(): iterable
    {
        return $this->pdo->query("SELECT id FROM migration")->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function prepareDataStructures(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migration (
                id VARCHAR(255) PRIMARY KEY
            );     
        ");
    }
}