<?php

namespace Phespro\Phespro\Security\Csrf;

class TokenGenerator implements  TokenGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(12));
    }
}