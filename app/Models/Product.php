<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
            $images = $product->images();

            // delete the images in the storage for the given product
            $images->each(function ($img){
                Storage::delete(ImageService::PRODUCT_FILE_DIRECTORY . $img->name);
            });

            // deletes the database records
            $images->delete();
        });
    }
}
