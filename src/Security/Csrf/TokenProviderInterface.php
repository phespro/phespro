<?php

namespace Phespro\Phespro\Security\Csrf;

interface TokenProviderInterface
{
    public function get(): string;
}