<?php

namespace RS\Instantiator;


class Singleton extends Child
{
    private $objectCache;

    public function getInstance(string $mode, bool $fallback, ...$args)
    {
        if($this->objectCache === null) {
            $this->objectCache = [];
        }
        if(!isset($this->objectCache[$mode])) {
            $factory = $this->getFactory($mode, $fallback);
            $this->objectCache[$mode] = $factory(...$args);
        }
        return $this->objectCache[$mode];
    }
}