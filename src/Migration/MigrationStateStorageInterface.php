<?php


namespace Phespro\Phespro\Migration;


interface MigrationStateStorageInterface
{
    /**
     * Marks a migration as passed.
     *
     * @param string $id
     * @return void
     */
    public function add(string $id): void;

    /**
     * Check whether given migration already passed execution
     *
     * @param string $id
     * @return bool
     */
    public function contains(string $id): bool;

    /**
     * Provides all already executes migrations.
     *
     * @return iterable<string> Already executed migration ids
     */
    public function getPassed(): iterable;

    /**
     * Executed before every migration run. Use this method to prepare storage structures (e.g. database table `migration`)
     *
     * @return void
     */
    public function prepareDataStructures(): void;
}
