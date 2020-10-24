<?php

namespace Tests\RS\Instantiator;


use PHPUnit\Framework\TestCase;
use RS\Instantiator\ContainerInterface;
use RS\Instantiator\Container;
use RS\Instantiator\Singleton;


class ContainerTest extends TestCase
{
    public function testInterface()
    {
        $container = new Container();
        $this->assertTrue($container instanceof ContainerInterface);
    }

    public function testHas()
    {
        $container = new Container();
        $this->assertFalse($container->has("some-class"));
    }

    public function testSet()
    {
        $container = new Container();
        $container->set("id", new Singleton([
            "default" => function() {
                return "testing set";
            }
        ]));
        $this->assertEquals(
            $container->get("id")
                ->getInstance("default", false),
            "testing set"
        );
    }

    public function testGet()
    {
        $container = new Container();
        $container->set("id", new Singleton([
            "default" => function() {
                return "testing get";
            }
        ]));
        $this->assertEquals(
            $container->get("id")
                ->getInstance("default", false),
            "testing get"
        );
    }
}