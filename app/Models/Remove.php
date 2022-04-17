<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remove extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en', 'name_ar', 'active'
    ];

    public function changeStatus()
    {
        return $this->update([
            'active' => $this->attributes['active'] === 1 ? 0 : 1
        ]);
    }
}
