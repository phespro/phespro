<?php

namespace Phespro\Phespro\Security\Csrf;

class MemoryTokenStorage implements TokenStorageInterface
{
    protected ?string $token = null;

    public function has(): bool
    {
        return $this->token !== null;
    }

    public function save(string $token): void
    {
        $this->token = $token;
    }

    public function get(): string
    {
        return $this->token;
    }
}