<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['id', 'updated_at'];

    protected $appends = ['original_price', 'sale_price'];

    public function getOriginalAttribute($value): string
    {
        return number_format($value / 100, 2);
    }

    public function getSaleAttribute($value): string
    {
        return number_format($value / 100, 2);
    }

    public function getOriginalPriceAttribute(): string
    {
        return $this->attributes['original'];
    }

    public function getSalePriceAttribute(): string
    {
        return $this->attributes['sale'];
    }

    public function getLanguageAttribute($value): string
    {
        return ucfirst($value);
    }
}
