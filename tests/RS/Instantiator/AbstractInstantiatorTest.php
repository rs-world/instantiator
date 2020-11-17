<?php

namespace Tests\RS\Instantiator;


use PHPUnit\Framework\TestCase;
use RS\Instantiator\InstantiatorInterface;
use RS\Instantiator\AbstractInstantiator;


class CalculatorInstantiator extends AbstractInstantiator
{
    public function __construct($mode=null, $fallback=null)
    {
        parent::__construct($mode, $fallback);
    }

    protected function register()
    {
        $this->instance([
            "default" => function($a, $b) {
                return $a + $b;
            },
            "sub" => function($a, $b) {
                return $a - $b;
            },
            "mul" => function($a, $b) {
                return $a * $b;
            },
            "div" => function($a, $b) {
                return intdiv($a, $b);
            }
        ]);
    }
    public function get($a, $b) {
        return $this->getInstance($a, $b);
    }
}


class SingletonCalculatorInstantiator extends AbstractInstantiator
{
    protected function register()
    {
        $this->singleton([
            "default" => function($a, $b) {
                return $a + $b;
            },
            "sub" => function($a, $b) {
                return $a - $b;
            },
            "mul" => function($a, $b) {
                return $a * $b;
            },
            "div" => function($a, $b) {
                return intdiv($a, $b);
            }
        ]);
    }
    public function get($a, $b) {
        return $this->getInstance($a, $b);
    }
}

class AbstractInstantiatorTest extends TestCase
{
    public function testInterface()
    {
        $calc = new CalculatorInstantiator();
        $this->assertTrue($calc instanceof AbstractInstantiator);
    }

    public function testSetGlobalMode()
    {
        AbstractInstantiator::reset();

        AbstractInstantiator::setGlobalMode("xyz");
        $this->assertEquals("xyz", AbstractInstantiator::getGlobalMode());
    }

    public function testSetGlobalFallback()
    {
        AbstractInstantiator::reset();

        AbstractInstantiator::setGlobalFallback(false);
        $this->assertEquals(false, AbstractInstantiator::getGlobalFallback());
    }


    public function testSetMode()
    {
        AbstractInstantiator::reset();

        // default mode performs add
        $calc = new CalculatorInstantiator();
        $this->assertEquals(6, $calc->get(2, 4));

        $calc->setMode("mul");
        $this->assertEquals(8, $calc->get(2, 4));

        // unknown modes fallbacks to default
        $calc->setMode("unknown-mode", true);
        $this->assertEquals(6, $calc->get(2, 4));
    }

    public function testGetMode()
    {
        AbstractInstantiator::reset();
        // default mode performs add
        $calc = new CalculatorInstantiator();
        $calc->setMode("xyz");
        $this->assertEquals("xyz", $calc->getMode());
    }

    public function testFunctionality()
    {
        AbstractInstantiator::reset();

        $add = new CalculatorInstantiator();
        $this->assertEquals(8, $add->get(3, 5));

        $alsoAdd = new CalculatorInstantiator("unknown-mode", true);
        $this->assertEquals(100, $alsoAdd->get(40, 60));

        $sub = new CalculatorInstantiator("sub");
        $this->assertEquals(12, $sub->get(-6, -18));
        $this->assertEquals(23, $sub->get(68, 45));

        $mul = new CalculatorInstantiator("mul", true);
        $this->assertEquals(6, $mul->get(6, 1));

        $div = new CalculatorInstantiator("div", true);
        $this->assertEquals(2, $div->get(8, 3));


        // singleton calculator instantiator test
        // note: whatever you assign on the first
        //       operation in a particular mode,
        //       would be always be returned for
        //       that particular mode
        //       whatever the input was the other
        //       times

        $add = new SingletonCalculatorInstantiator();
        $this->assertEquals(15, $add->get(8, 7));
        // no matter what the input is, $add->get would
        // always return 15
        $this->assertEquals(15, $add->get(45, 23));

        // notice: the result would change this time
        //         though it is add, cause mode changed
        //         singleton works based on mode name
        //         remember it
        $alsoAdd = new SingletonCalculatorInstantiator("unknown-mode", true);
        $this->assertEquals(100, $alsoAdd->get(40, 60));
        $this->assertEquals(100, $alsoAdd->get(56, 5));

        $sub = new SingletonCalculatorInstantiator("sub");
        $this->assertEquals(12, $sub->get(-6, -18));
        $this->assertEquals(12, $sub->get(68, 45));


        // now, check change in global mode
        // change mode to "mul"
        AbstractInstantiator::setGlobalMode("mul");

        // notice, no mode provided
        // it would be set to mul mode
        $mul = new SingletonCalculatorInstantiator();
        $this->assertEquals(12, $mul->get(6, 2));
        $this->assertEquals(12, $mul->get(18, 4));
    }
}