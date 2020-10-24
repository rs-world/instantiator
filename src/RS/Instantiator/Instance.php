<?php

namespace RS\Instantiator;


class Instance extends Child
{   
    public function getInstance(string $mode, bool $fallback, ...$args)
    {
        $factory = $this->getFactory($mode, $fallback);
        return $factory(...$args);
    }
}