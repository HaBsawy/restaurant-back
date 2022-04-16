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

    public function changeStatus()
    {
        return $this->update([
            'active' => $this->attributes['active'] === 1 ? 0 : 1
        ]);
    }
}
