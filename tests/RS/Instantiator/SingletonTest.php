<?php

namespace Tests\RS\Instantiator;


use PHPUnit\Framework\TestCase;
use RS\Instantiator\ChildInterface;
use RS\Instantiator\Singleton;


class SingletonTest extends TestCase
{
    public function testInterface()
    {
        $singleton = new Singleton([]);
        $this->assertTrue($singleton instanceof ChildInterface);
    }

    public function testGetInstance()
    {
        $factories = [
            "default" => function($a, $b) { // default returns mul
                return $a * $b;
            },
            "test" => function($a, $b) { // test returns div
                return intdiv($a, $b);
            }
        ];

        $singleton = new Singleton($factories);

        $mulRes = $singleton->getInstance("default", false, 2, 3);
        $this->assertEquals(6, $mulRes);

        // notice, singleton still returns 6, not 15
        $anotherMulRes = $singleton->getInstance("default", false, 5, 3);
        $this->assertEquals(6, $anotherMulRes);

        // notice for a new mode name you'll get the changed value
        $unknownRes = $singleton->getInstance("unknown-mode", true, 5, 3);
        $this->assertEquals(15, $unknownRes);

        $divRes = $singleton->getInstance("test", false, 8, 3);
        $this->assertEquals(2, $divRes);
    }
}