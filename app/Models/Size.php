<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en', 'name_ar', 'price', 'discount', 'active'
    ];

    protected $appends = ['has_discount'];

    public function getHasDiscountAttribute(): string
    {
        return $this->attributes['discount'] > 0 ? '1' : '0';
    }

    public function changeStatus(): bool
    {
        return $this->update([
            'active' => $this->attributes['active'] === 1 ? 0 : 1
        ]);
    }
}
