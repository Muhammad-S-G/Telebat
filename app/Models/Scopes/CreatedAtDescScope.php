<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CreatedAtDescScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (empty($builder->getQuery()->orders)) {
            $builder->orderBy('created_at', 'desc');
        }
    }
}
