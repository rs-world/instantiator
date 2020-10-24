<?php

namespace Tests\RS\Instantiator;


use PHPUnit\Framework\TestCase;
use RS\Instantiator\ChildInterface;
use RS\Instantiator\Child;


class Multiply
{
    private $a;
    private $b;

    public function __construct(int $a, int $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function result(): int
    {
        return $this->a * $this->b;
    }
}

class Divide
{
    private $a;
    private $b;

    public function __construct(int $a, int $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function result(): int
    {
        return intdiv($this->a, $this->b);
    }
}


class SomeChild extends Child
{
    public function getInstance(string $mode, bool $fallback, ...$args)
    {
        $factory = $this->getFactory($mode, $fallback);
        return $factory(...$args);
    }
}


class ChildTest extends TestCase
{
    public function testInterface()
    {
        $child = new SomeChild([]);
        $this->assertTrue($child instanceof ChildInterface);
    }

    public function testGetInstance()
    {
        $factories = [
            "default" => function($a, $b) {
                return new Multiply($a, $b);
            },
            "test" => function($a, $b) {
                return new Divide($a, $b);
            }
        ];

        $child = new SomeChild($factories);

        $mul = $child->getInstance("default", true, 3, 4);
        $this->assertEquals(12, $mul->result());

        $div = $child->getInstance("test", false, 8, 2);
        $this->assertEquals(4, $div->result());

        $div = $child->getInstance("test", false, 8, 3);
        $this->assertEquals(2, $div->result());

        $mul = $child->getInstance("unknow-mode", true, 5, 10);
        $this->assertEquals(50, $mul->result());
    }
}