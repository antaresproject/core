<?php

namespace Antares\Foundation\Events\Contract;

interface Event
{

    public static function getName(): string;
    public static function isCountable(): bool;
    public static function getDescription(): string;

}
