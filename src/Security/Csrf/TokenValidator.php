<?php

namespace Phespro\Phespro\Security\Csrf;

class TokenValidator implements TokenValidatorInterface
{
    public function validate(string $expected, string $actual): bool
    {
        return hash_equals($expected, $actual);
    }
}