<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'user-id',
            'description'
        ];

    public function getProductsRelation(){
        return $this->hasMany('App\Models\Product', 'shop-id', 'id');
    }
}
