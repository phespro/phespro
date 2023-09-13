<?php

namespace Phespro\Phespro\Security\Csrf;

class PhpSessionTokenStorage implements TokenStorageInterface
{
    protected const STORAGE_KEY = 'csrf_token';

    public function has(): bool
    {
        $this->ensureSession();
        return isset($_SESSION[static::STORAGE_KEY]);
    }

    public function save(string $token): void
    {
        $this->ensureSession();
        $_SESSION[static::STORAGE_KEY] = $token;
    }

    public function get(): string
    {
        $this->ensureSession();
        return $_SESSION[static::STORAGE_KEY] ?? throw new TokenMissingException;
    }

    protected function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            session_start();
        }
    }
}