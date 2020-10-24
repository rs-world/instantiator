<?php

namespace RS\Instantiator;


abstract class Instantiator implements InstantiatorInterface
{
    // global i.e. static properties and methods of instantiator

    // global state variables and
    // container for child instantiators
    // i.e. classes which extends Instantiator
    private static $globalMode;
    private static $globalFallback;
    private static $container;

    private static function globalMode(): string
    {
        if(self::$globalMode === null) { // if not set
            self::$globalMode = "default"; // then, "default"
        }
        return self::$globalMode;
    }

    private static function globalFallback(): bool
    {
        if(self::$globalFallback === null) { // if not set
            self::$globalFallback = true; // then, true
        }
        return self::$globalFallback;
    }

    private static function container(): ContainerInterface
    {
        if(self::$container === null) {
            self::$container = new Container();
        }
        return self::$container;
    }

    private static function checkGlobalMethodAccessPermission(string $methodName): void
    {
        if(get_called_class() !== Instantiator::class) {
            throw new \Exception(
                "Error: \"" . get_called_class()
                . "\" does not have access to \""
                . $methodName . "\" method",
                1
            );    
        }
    }

    public static function setGlobalMode(string $globalMode): void
    {
        self::checkGlobalMethodAccessPermission(__FUNCTION__);
        self::$globalMode = $globalMode;
    }

    public static function getGlobalMode(): string
    {
        self::checkGlobalMethodAccessPermission(__FUNCTION__);
        return self::globalMode();
    }

    public static function setGlobalFallback(bool $globalFallback): void
    {
        self::checkGlobalMethodAccessPermission(__FUNCTION__);
        self::$globalFallback = $globalFallback;
    }

    public static function getGlobalFallback(): bool
    {
        self::checkGlobalMethodAccessPermission(__FUNCTION__);
        return self::globalFallback();
    }

    public static function reset(): void
    {
        self::checkGlobalMethodAccessPermission(__FUNCTION__);
        self::$globalMode = "default";
        self::$globalFallback = true;
        self::$container = null;
    }


    // local properties and methods of instantiator

    // local state variables
    private $childMode;
    private $childFallback;
    private $childType; // it is used to determine type of child, i.e. instance or singleton
    private $childFactories;

    public function __construct(?string $mode=null, ?bool $fallback=null)
    {
        $this->childMode = ($mode === null) ? self::globalMode() : $mode;
        $this->childFallback = ($fallback === null) ? self::globalFallback() : $fallback;
    }

    // check for errors
    // if instance or singleton called twice or more
    // or both called in a row
    // it throws error
    // it also assigns $this->childType a proper value, if
    // no error occurs
    private function checkChildType(string $type): void
    {
        if($this->childType === null) {
            $this->childType = $type;
        } else {
            throw new \Exception(
                "Error: \"instance()\" or \"signleton()\" can only be used once inside register method",
                1
            );
        }
    }

    protected function instance(array $factories): void
    {
        $this->checkChildType("instance");
        $this->childFactories = $factories;
    }

    protected function singleton(array $factories): void
    {
        $this->checkChildType("singleton");
        $this->childFactories = $factories;
    }

    // `register` is an abstract method which must be implemented by
    // user. It should be used to provide closures for instantiating
    // objects for various modes
    abstract protected function register();

    // search in all factories or closures for default mode
    // if not found throws error
    private function checkDefaultMode(): void
    {
        if(!isset($this->childFactories["default"])) {
            throw new \Exception(
                "Error: default mode not found while registering \"" . get_called_class() . "\"",
                1
            );
        }
    }

    // search in all factories or closures for default mode
    // if any invalid closure found, then throws error
    private function checkChildFactories(): void
    {
        foreach($this->childFactories as $mode => $factory) {
            if(!($factory instanceof \Closure)) {
                throw new \Exception(
                    "Error: mode \"" . $mode . "\" found with an invalid closure while regstering\"" . get_called_class() . "\"",
                    1
                );
            }
        }
    }


    public function setMode(string $mode): void
    {
        $this->childMode = $mode;
    }

    public function getMode(): string
    {
        return $this->childMode;
    }

    public function setFallback(bool $fallback): void
    {
        $this->fallback = $fallback;
    }

    public function getFallback(): bool
    {
        return $this->childFallback;
    }

    final protected function getInstance(...$args)
    {
        $container = self::container();
        $childName = get_called_class();
        if(!$container->has($childName)) {
            // flushing $this->childType
            //      and $this->childFactories
            $this->childType = null; // instance or singleton
            $this->childFactories = null; // [mode => closure] i.e. array with "mode" => "closure" mapping

            // retrieving childType and childFactories
            // instance or sigleton method does the job for us
            // based upon which is called
            $this->register();

            // check for errors
            $this->checkDefaultMode();
            $this->checkChildFactories();

            if($this->childType === "instance") {
                $child = new Instance($this->childFactories);
            } else {
                $child = new Singleton($this->childFactories);
            }
            $container->set($childName, $child);
        }

        return $container->get($childName)->getInstance(
            $this->getMode(),
            $this->getFallback(),
            ...$args
        );
    }
}