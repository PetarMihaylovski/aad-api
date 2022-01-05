<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'name',
            'user_id',
            'description',
            'image_url',
            'image_name'
        ];

    public function owner(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(){
        return$this->hasMany(Order::class, "shop_id");
    }

    public function products()
    {
        return $this->hasMany(Product::class, "shop_id");
    }

    /**
     * called before the shop instance gets deleted.
     *
     * deletes all the products for a given shop
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($shop) {
            //deletes the stored image file
            Storage::delete(ImageService::SHOP_FILE_DIRECTORY . $shop->image_name);

            // deletes every product one by one,
            // so the before-delete callback in product
            // gets called
            // if I bulk delete, the before-delete callback
            // won't be called
            $shop->products()->each(function ($product) {
                $product->delete();
            });
        });
    }
}
