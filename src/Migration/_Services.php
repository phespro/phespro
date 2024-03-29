<?php

namespace Phespro\Phespro\Migration;

use Phespro\Phespro\Kernel;
use Phespro\Phespro\Migration\MigrationStateStorage\MemoryMigrationStateStorage;
use Psr\Log\LoggerInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(MigrationStateStorageInterface::class, fn() => new MemoryMigrationStateStorage());

        $kernel->add(CliMigratorInterface::class, fn() => new CliMigrator(
            $kernel->getObject(MigrationStateStorageInterface::class),
            $kernel->getByTag('migration'),
            $kernel->getObject(LoggerInterface::class),
        ));

        Commands\_Services::register($kernel);
    }
}