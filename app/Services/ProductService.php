<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;

class ProductService
{

    /**
     * @param int $shopId
     * @return mixed
     */
    public function getProductsForShop(Shop $shop)
    {
        return Product::where('shop_id', $shop['id'])->get();
    }

    public function saveProduct($shopId, $name, $price, $stock, $category):Product {
        return Product::create([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'shop_id' => $shopId,
            'category' => $category,
        ]);
    }
}
