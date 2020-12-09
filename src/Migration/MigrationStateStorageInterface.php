<?php


namespace Phespro\Phespro\Migration;


interface MigrationStateStorageInterface
{
    function add(string $id): void;
    function contains(string $id): bool;
}