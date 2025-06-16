<?php

namespace App\Traits;

use App\Models\Scopes\CreatedAtDescScope;

trait CreatedAtDescScopeTrait
{
    protected static function bootCreatedAtDescScopeTrait()
    {
        static::addGlobalScope(new CreatedAtDescScope);
    }
}
