<?php

namespace Phespro\Phespro\Security\Csrf;

use Exception;

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

    /**
     * @throws Exception
     */
    protected function ensureSession(): void
    {
        $sessionStatus = session_status();

        if ($sessionStatus === PHP_SESSION_DISABLED) {
            throw new Exception("Cannot start session, since sessions are disabled");
        }

        if ($sessionStatus === PHP_SESSION_ACTIVE) {
            return;
        }

        session_start();
    }
}