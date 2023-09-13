<?php

namespace Phespro\Phespro\Security\Csrf;

interface TokenGeneratorInterface
{
    public function generate(): string;
}