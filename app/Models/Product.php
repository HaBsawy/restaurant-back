<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 *
 * @mixin Builder
 * @package App\Models
 * @property int $id
 * @property int $category_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $description_en
 * @property string $description_ar
 * @property string $price
 * @property int $discount
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read mixed $has_discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Image|null $main_image
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereActive($value)
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDescriptionAr($value)
 * @method static Builder|Product whereDescriptionEn($value)
 * @method static Builder|Product whereDiscount($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereNameAr($value)
 * @method static Builder|Product whereNameEn($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereUpdatedAt($value)
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name_en', 'name_ar', 'description_en', 'description_ar', 'price',
        'discount', 'active'
    ];

    protected $with = [
        'main_image', 'images'
    ];

    protected $appends = [
        'has_discount'
    ];

    public function changeStatus()
    {
        return $this->update([
            'active' => $this->attributes['active'] === 1 ? 0 : 1
        ]);
    }

    public function getHasDiscountAttribute()
    {
        return (bool)$this->attributes['discount'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function main_image()
    {
        return $this->morphOne(Image::class, 'model')->where('type', 'main');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'model')->where('type', 'default');
    }
}
