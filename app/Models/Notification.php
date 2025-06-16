<?php

namespace App\Models;

use App\Traits\CreatedAtDescScopeTrait;
use App\Traits\DateFormatTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    use HasFactory, DateFormatTrait, CreatedAtDescScopeTrait;

    protected $fillable = [
        'type',
        'notifiable_id',
        'notifiable_type',
        'data'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function asNotification()
    {
        $type = new $this->type;

        foreach ($this->attributes as $key => $value) {
            $type->setAttribute($key, $value);
        }
        return $type;
    }

    public function scopeFilter(Builder $builder, $user_id = null, $not_read = null)
    {
        if ($user_id !== null) {
            $builder->where('notifiable_id', $user_id);
        }

        if ($not_read !== null) {
            $builder->whereNull('read_at');
        }
    }
}
