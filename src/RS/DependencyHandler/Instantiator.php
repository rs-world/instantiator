<?php

namespace RS\DependencyHandler;


abstract class Instantiator implements InstantiatorInterface
{
    private static $store;
    private static $cache;
    
    private static function called_class()
    {
        return get_called_class();
    }

    private static function &store()
    {
        if(self::$store === null) {
            self::$store = [];
            self::called_class()::instantiates();
        }
        return self::$store;
    }

    private static function &cache()
    {
        if(self::$cache === null) {
            self::$cache = [];
        }
        return self::$cache;
    }

    public static function add($trigger, $builder)
    {
        if(isset(self::store()[$trigger])) {
            throw new \Exception(
                $trigger . " already exists in " . self::called_class(),
                1
            );
        }
        self::store()[$trigger] = [
            "class" => $builder[0],
            "method" => $builder[1]
        ];
    }

    public static function addOrOverwrite($trigger, $builder)
    {
        self::store()[$trigger] = [
            "class" => $builder[0],
            "method" => $builder[1]
        ];
        unset(self::cache()[$trigger]);
    }

    public static function get($trigger, ...$args)
    {
        if(!isset(self::store()[$trigger])) {
            throw new \Exception(
                "No such class named " . $trigger . " registered in " . self::called_class(),
                1
            );
        }
        if(!isset(self::cache()[$trigger])) {
            $builder = self::store()[$trigger]["class"];
            self::cache()[$trigger] = new $builder();
        }
        return self::cache()[$trigger]->{
            self::store()[$trigger]["method"]
        }(...$args);
    }

    public static function flush()
    {
        self::$store = null;
        self::$cache = null;
    }
}