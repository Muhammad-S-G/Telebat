<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFormatTrait
{
    protected $newDateFormat = 'd-m-Y h:i A';

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->format($this->newDateFormat);
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->format($this->newDateFormat);
    }

    public function getReadAtAttribute($value)
    {
        return Carbon::parse($value)
            ->format($this->newDateFormat);
    }
}
