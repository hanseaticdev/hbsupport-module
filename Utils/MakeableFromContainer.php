<?php

namespace Modules\HbSupport\Utils;

trait MakeableFromContainer
{
    public static function make(): static
    {
        return app(static::class);
    }
}
