<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @mixin Builder
 * @package App\Models
 * @property int $id
 * @property string $name_en
 * @property string $name_ar
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereActive($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereNameAr($value)
 * @method static Builder|Category whereNameEn($value)
 * @method static Builder|Category whereUpdatedAt($value)
 */
class Category extends Model
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
