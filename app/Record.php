<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = [];
    protected $hidden = ['id','updated_at'];

    public function getOriginalAttribute($value)
    {
        return number_format($value / 100, 2);
    }

    public function getSaleAttribute($value)
    {
        return number_format($value / 100, 2);
    }

}
