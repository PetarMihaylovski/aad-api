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
            'category'
        ];

    public function shop(){
        return $this->belongsTo(Shop::class, "shop_id");
    }

    public function images(){
        return $this->hasMany(Image::class, "product_id");
    }
}
