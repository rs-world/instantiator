<?php

namespace RS\Instantiator;


interface ContainerInterface
{
    public function __construct();
    public function has(string $id): bool;
    public function set(string $id, ChildInterface $val): void;
    public function get(string $id): ChildInterface;
}