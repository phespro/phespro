<?php


namespace Phespro\Phespro\Migration;

use MemoryMigrationStateStorage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CliMigrator implements CliMigratorInterface
{
    public function __construct(
        protected MigrationStateStorageInterface $migrationStateStorage,
        protected array $migrations,
        protected LoggerInterface $logger,
    )
    {
    }

    function applyAll(InputInterface $input, OutputInterface $output): void
    {
        if ($this->migrationStateStorage instanceof MemoryMigrationStateStorage) {
            $this->logger->warning(MemoryMigrationStateStorage::class . ' is only intended for testing');
        }

        $this->migrationStateStorage->prepareDataStructures();

        $migrations = array_filter(
            $this->migrations,
            fn(MigrationInterface $migration) => !$this->migrationStateStorage->contains($migration->getId())
        );

        $io = new SymfonyStyle($input, $output);

        if (empty($migrations)) {
            $io->comment('No migrations to execute');
            return;
        }

        usort($migrations, fn(MigrationInterface $a, MigrationInterface $b) => $a->getId() <=> $b->getId());

        foreach ($migrations as $migration) {
            assert($migration instanceof MigrationInterface);
            $io->writeln('');
            $io->writeln("# Execute migration {$migration->getId()}");
            $io->writeln($migration->getDescription());
            $migration->execute();
            $this->migrationStateStorage->add($migration->getId());
        }

        $io->success('All migrations applied successful');
    }
}
