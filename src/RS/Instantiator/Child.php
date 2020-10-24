<?php

namespace RS\Instantiator;


abstract class Child implements ChildInterface
{
    protected $factories;

    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    protected function getFactory(string $mode, bool $fallback): \Closure
    {
        if(!isset($this->factories[$mode])) {
            if($fallback !== true) {
                throw new \Exception(
                    "Error: No mode found with name \"" . $mode
                    . "\" and also fallback is not set to \"true\"",
                    1
                );
            }
            return $this->factories["default"];
        }
        return $this->factories[$mode];
    }
}