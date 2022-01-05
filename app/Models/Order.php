<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'user_id',
            'shop_id',
        ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function items(){
        return $this->hasMany(OrderProduct::class, 'order_id');
    }
}
