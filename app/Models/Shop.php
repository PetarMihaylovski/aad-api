<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'name',
            'user_id',
            'description',
            'image_url'
        ];

    public function products(){
        return $this->hasMany(Product::class, "shop_id");
    }
}
