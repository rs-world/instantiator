<?php

namespace RS\DependencyHandler;


interface InstantiatorInterface
{
    public static function add($trigger, $builder);
    public static function addOrOverwrite($trigger, $builder);
    public static function instantiates();
    public static function get($trigger, ...$args);
    public static function flush();
}