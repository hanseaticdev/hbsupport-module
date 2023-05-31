<?php

namespace Modules\HbSupport\Utils;

trait Makeable
{
    public static function make(): static
    {
        return new static(...func_get_args());
    }
}
