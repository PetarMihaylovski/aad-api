<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'name',
            'price',
            'stock',
            'category',
            'shop_id'
        ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, "shop_id");
    }

    public function images()
    {
        return $this->hasMany(Image::class, "product_id");
    }


    /**
     * called before the instance gets deleted.
     *
     * deletes all the images for a given product, before the product itself gets deleted
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            $product->images()->delete();
        });
    }
}
