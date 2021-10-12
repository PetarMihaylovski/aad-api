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

    public function getProductsRelation(){
        return $this->hasMany('App\Models\Product', 'shop_id', 'id');
    }
}
