<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations;

trait HasArabicTrans
{
    use HasTranslations;

    public function getTitleArAttribute()
    {
        return $this->getTranslation('title', 'ar');
    }

    public function setTitleArAttribute(string $value)
    {
        return $this->setTranslation('title', 'ar', $value);
    }

    public function getDescriptionArAttribute()
    {
        return $this->getTranslation('description', 'ar');
    }

    public function setDescriptionArAttribute($value)
    {
        return $this->setTranslation('description', 'ar', $value);
    }
}
