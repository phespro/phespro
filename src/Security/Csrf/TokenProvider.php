<?php

namespace Phespro\Phespro\Security\Csrf;

readonly class TokenProvider implements TokenProviderInterface
{
    public function __construct(
        protected TokenGeneratorInterface $csrfTokenGenerator,
        protected TokenStorageInterface   $csrfTokenStorage,
    )
    {
    }

    public function get(): string
    {
        if ($this->csrfTokenStorage->has()) {
            return $this->csrfTokenStorage->get();
        }
        $token = $this->csrfTokenGenerator->generate();
        $this->csrfTokenStorage->save($token);
        return $token;
    }
}