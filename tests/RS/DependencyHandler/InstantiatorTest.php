<?php

namespace Tests\RS\DepenencyHandler;


use PHPUnit\Framework\TestCase;
use RS\DependencyHandler\InstantiatorInterface;
use RS\DependencyHandler\Instantiator;


class InstantiatorTest extends TestCase
{
    public function testInterface()
    {
        $instantiator = new Instantiator();
        $this->assertTrue($instantiator instanceof InstantiatorInterface);
    }
}