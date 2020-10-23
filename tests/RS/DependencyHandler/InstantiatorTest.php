<?php

namespace Tests\RS\DepenencyHandler;


use PHPUnit\Framework\TestCase;
use RS\DependencyHandler\InstantiatorInterface;
use RS\DependencyHandler\Instantiator;


// basic classes

class Singleton {
    public function says() {
        return "singleton";
    }
}


class Speaker
{
    private $name;
    private $speech;

    public function __construct($name, $speech=null)
    {
        $this->name = $name;
        if($speech === null) {
            $speech = "Hello, World!";
        }
        $this->speech = $speech;
    }

    public function getName() {
        return $this->name;
    }

    public function speaks()
    {
        return $this->speech;
    }
}


class Presenter
{
    private $name;
    private $act;

    public function __construct($name, $act=null)
    {
        $this->name = $name;
        if($act === null) {
            $act = "The greatest show on earth";
        }
        $this->act = $act;
    }

    public function getName() {
        return $this->name;
    }

    public function presents()
    {
        return $this->act;
    }
}


// builder classes

class SingletonBuilder
{
    private $instance;

    final public function build()
    {
        if($this->instance === null) {
            $this->instance = new Singleton();
        }
        return $this->instance;
    }
}


class SpeakerBuilder {
    public function build($name, $speech=null)
    {
        return new Speaker($name, $speech);
    }
}


class PresenterBuilder {
    public function build($name, $act=null)
    {
        return new Presenter($name, $act);
    }
}


// instantiator

class SomeInstantiator extends Instantiator
{
    public static function instantiates()
    {
        self::add("speaker", [SpeakerBuilder::class, "build"]);
    }
}


// test instantiator

class InstantiatorTest extends TestCase
{
    public function testInterface()
    {
        $instantiator = new SomeInstantiator();
        $this->assertTrue($instantiator instanceof InstantiatorInterface);
    }

    public function testGet()
    {
        SomeInstantiator::flush();
        // add instantiators
        // also note: "speaker" instantiator is already added
        //            using the instantiates method
        SomeInstantiator::add("singleton", [SingletonBuilder::class, "build"]);

        $singleton = SomeInstantiator::get(
            "singleton"
        );
        $this->assertTrue($singleton instanceof Singleton);
        $this->assertEquals($singleton->says(), "singleton");
        
        $speaker = SomeInstantiator::get(
            "speaker",
            "reyad"
        );
        $this->assertTrue($speaker instanceof Speaker);
        $this->assertEquals($speaker->getName(), "reyad");

        $speaker = SomeInstantiator::get(
            "speaker",
            "reyad",
            "We're seeking for peace!"
        );
        $this->assertEquals($speaker->getName(), "reyad");
        $this->assertEquals($speaker->speaks(), "We're seeking for peace!");

        $speaker = SomeInstantiator::get(
            "speaker",
            "jen",
            "What a nice day!"
        );
        $this->assertEquals($speaker->getName(), "jen");
        $this->assertEquals($speaker->speaks(), "What a nice day!");
    }

    public function testAddOrOverwrite() {
        SomeInstantiator::flush();

        SomeInstantiator::add(
            "singleton",
            [SingletonBuilder::class, "build"]
        );
        $singleton = SomeInstantiator::get(
            "singleton"
        );
        $this->assertTrue($singleton instanceof Singleton);
        $this->assertEquals($singleton->says(), "singleton");
        

        // overwriting "singleton" by "speaker" class
        // a "speaker" object will be built by "singleton" trigger
        // so, basically what we're doing is
        //     we're creating a "speaker" class using
        //     the singleton trigger
        SomeInstantiator::addOrOverwrite(
            "singleton",
            [SpeakerBuilder::class, "build"]
        );
        $speaker = SomeInstantiator::get(
            "singleton",
            "reyad"
        );
        $this->assertTrue($speaker instanceof Speaker);
        $this->assertEquals($speaker->getName(), "reyad");
    }
}