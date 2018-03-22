<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    /**
     * Attributes that cannot be mass assigned.
     */
    protected $guarded = [];

    /**
     * Attributes that are hidden from json.
     */
    protected $hidden = ['id', 'updated_at'];

    /**
     * Format the original price.
     *
     * @param $value
     *
     * @return string
     */
    public function getOriginalAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * Format the sale price.
     *
     * @param $value
     *
     * @return string
     */
    public function getSaleAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * Get the language code.
     *
     * @param $value
     *
     * @return string
     */
    public function getLanguageAttribute($value)
    {
        return ucfirst($value);
    }
}
