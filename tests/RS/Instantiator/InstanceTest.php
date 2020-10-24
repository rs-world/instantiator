<?php

namespace Tests\RS\Instantiator;


use PHPUnit\Framework\TestCase;
use RS\Instantiator\ChildInterface;
use RS\Instantiator\Instance;


class InstanceTest extends TestCase
{
    public function testInterface()
    {
        $instance = new Instance([]);
        $this->assertTrue($instance instanceof ChildInterface);
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

        $instance = new Instance($factories);

        $mulRes = $instance->getInstance("default", false, 2, 3);
        $this->assertEquals(6, $mulRes);

        $anotherMulRes = $instance->getInstance("default", false, 5, 3);
        $this->assertEquals(15, $anotherMulRes);

        $unknownRes = $instance->getInstance("unknown-mode", true, 7, 8);
        $this->assertEquals(56, $unknownRes);

        $divRes = $instance->getInstance("test", false, 8, 3);
        $this->assertEquals(2, $divRes);
    }
}