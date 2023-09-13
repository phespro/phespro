<?php

namespace Phespro\Phespro\Security\Csrf;

interface TokenStorageInterface
{
    public function has(): bool;
    public function save(string $token): void;
    public function get(): string;
}