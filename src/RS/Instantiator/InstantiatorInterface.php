<?php

namespace RS\Instantiator;


interface InstantiatorInterface
{
    public static function setGlobalMode(string $globalMode);
    public static function getGlobalMode(): string;
    public static function setGlobalFallback(bool $globalFallback): void;
    public static function getGlobalFallback(): bool;
    public static function reset(): void;
}