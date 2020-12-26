<?php


namespace Phespro\Phespro\Migration;


interface MigrationInterface
{
    function getId(): string;
    function getDescription(): string;
    function execute(): void;
}
