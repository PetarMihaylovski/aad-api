<?php
/**
 * Created by PhpStorm.
 * User: Niek
 * Date: 28-10-2021
 * Time: 12:22
 */

namespace Tests;


use App\Models\Product;
use App\Models\Shop;
use App\Models\User;

class HelperMethods
{
    public static function registerUser(){
        $user = User::create([
            'username' => 'test',
            'email' => 'test@gmail.com',
            'password' => bcrypt('test1234@'),
            'address' => 'Westerhoeve 435',
            'postal' => '7628BF',
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return $response;
    }

    public static function registerShop($id){
        $shop = Shop::create([
            'name' => 'test webshop',
            'user_id' => $id,
            'description' => 'This is a test description',
            'image_url' => ''
        ]);

        return $shop;
    }

    public static function addProduct($id){
        $product = Product::create([
           'name' => 'Adidas shirt',
           'price' => 5.55,
           'stock' => 5,
           'shop_id' => $id,
           'category' => 'shirts'
        ]);

        return $product;
    }
}