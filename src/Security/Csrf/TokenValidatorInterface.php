<?php

namespace Phespro\Phespro\Security\Csrf;

interface TokenValidatorInterface
{
    public function validate(string $expected, string $actual): bool;
}