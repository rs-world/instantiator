<?php

namespace RS\Instantiator;


interface ChildInterface
{
    public function __construct(array $factories);
    public function getInstance(string $mode, bool $fallback, ...$args);
}