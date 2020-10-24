<?php

namespace RS\Instantiator;


class Container implements ContainerInterface
{
    private $cache;

    public function __construct()
    {
        $this->cache = [];
    }

    public function has(string $id): bool
    {
        return isset($this->cache[$id]);
    }

    public function set(string $id, ChildInterface $val): void
    {
        $this->cache[$id] = $val;
    }

    public function get(string $id): ChildInterface
    {
        if(!isset($this->cache[$id])) {
            throw new \Exception(
                "Error: id \"" . $id . "\" not found in container",
                1
            );
        }
        return $this->cache[$id];
    }
}