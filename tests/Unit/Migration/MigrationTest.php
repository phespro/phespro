<?php


namespace Phespro\Phespro\Tests\Unit\Migration;


use Phespro\Phespro\Kernel;
use Phespro\Phespro\Migration\Commands\ApplyAllCommand;
use Phespro\Phespro\Migration\MigrationInterface;
use Phespro\Phespro\Migration\MigrationStateStorageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MigrationTest extends TestCase
{
    /**
     * @covers \Phespro\Phespro\Migration\Commands\ApplyAllCommand
     * @covers \Phespro\Phespro\Migration\CliMigrator
     */
    function test()
    {
        $kernel = new Kernel;

        $kernel->add(
            'testmigration1',
            fn() => new class implements MigrationInterface
            {
                function getId(): string
                {
                    return '1';
                }

                function getDescription(): string
                {
                    return 'Test Migration 1';
                }

                function execute(): void
                {
                    // irrelevant
                }
            },
            ['migration']
        );
        $kernel->add(
            'testmigration2',
            fn() => new class implements MigrationInterface
            {
                function getId(): string
                {
                    return '2';
                }

                function getDescription(): string
                {
                    return 'Test Migration 2';
                }

                function execute(): void
                {
                    // irrelevant
                }
            },
            ['migration']
        );

        $commandTester = new CommandTester($kernel->getObject(ApplyAllCommand::class));

        $commandTester->execute([]);

        $commandTester->getDisplay();

        $migrationStateStorage = $kernel->getObject(MigrationStateStorageInterface::class);
        $this->assertTrue($migrationStateStorage->contains('1'));
        $this->assertTrue($migrationStateStorage->contains('2'));
        $this->assertFalse($migrationStateStorage->contains('3'));
    }
}