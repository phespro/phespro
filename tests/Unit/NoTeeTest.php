<?php

namespace Phespro\Phespro\Tests\Unit;

use Phespro\Phespro\Kernel;
use PHPUnit\Framework\TestCase;

class NoTeeTest extends TestCase
{
    public function test()
    {
        $kernel = new Kernel;
        $kernel->add('myService', fn() => new NoTeeTraitUsingService);
        $service = $kernel->get('myService');
        assert($service instanceof NoTeeTraitUsingService);
        $this->assertNotNull($service->noTee);
    }
}