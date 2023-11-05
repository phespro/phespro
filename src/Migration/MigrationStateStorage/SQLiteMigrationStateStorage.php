<?php


namespace Phespro\Phespro\Migration\MigrationStateStorage;


use Phespro\Phespro\Migration\MigrationStateStorageInterface;

readonly class SQLiteMigrationStateStorage implements MigrationStateStorageInterface
{
    public function __construct(
        protected \PDO $pdo
    )
    {
    }

    function add(string $id): void
    {
        $this->pdo->prepare("
            INSERT INTO migrations VALUES (:id)
        ")->execute(['id' => $id]);
    }

    function contains(string $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM migrations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return !!$stmt->fetchColumn();
    }

    public function getPassed(): iterable
    {
        return $this->pdo->query("SELECT id FROM migrations")->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function prepareDataStructures(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id TEXT PRIMARY KEY                
            );
        ");
    }
}
