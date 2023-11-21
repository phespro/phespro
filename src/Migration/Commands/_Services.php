<?php

namespace Phespro\Phespro\Migration\Commands;

use Phespro\Phespro\Kernel;
use Phespro\Phespro\Migration\CliMigratorInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(
            ApplyAllCommand::class,
            fn() => new ApplyAllCommand($kernel->get(CliMigratorInterface::class)),
            ['cli_command']
        );

        $kernel->add(
            CreateCommand::class,
            fn() => new CreateCommand,
            ['cli_command'],
        );

    }
}